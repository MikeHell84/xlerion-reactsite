<?php
// Apply safe, non-destructive migrations to local test DB.
// - Creates missing tables from remote_schema_only_from_json.sql when available (sanitizes defaults)
// - Adds missing columns as NULLABLE without risky defaults
// - DOES NOT MODIFY existing columns (no ALTER MODIFY)

require_once __DIR__ . '/../includes/config.php';
$env = load_env(__DIR__ . '/../.env');
$host = $env['LOCAL_DB_HOST'] ?? '127.0.0.1';
$port = $env['LOCAL_DB_PORT'] ?? 3306;
$db = $env['LOCAL_DB_DATABASE'] ?? 'xlerionc_xlerion_db_test';
$user = $env['LOCAL_DB_USERNAME'] ?? 'root';
$pass = $env['LOCAL_DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    fwrite(STDERR, "Cannot connect to local DB: " . $e->getMessage() . "\n");
    exit(1);
}

$remoteSchema = json_decode(file_get_contents(__DIR__ . '/../data/remote_schema.json'), true);
$schemaSql = file_exists(__DIR__ . '/../remote_schema_only_from_json.sql') ? file_get_contents(__DIR__ . '/../remote_schema_only_from_json.sql') : '';

$applied = [];

foreach ($remoteSchema as $table => $meta) {
    // check table exists
    $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
    $stmt->execute([$table]);
    $exists = (bool)$stmt->fetchColumn();

    if (!$exists) {
        // try extract CREATE TABLE from schemaSql
        $create = '';
        if ($schemaSql) {
            $pattern = '/DROP TABLE IF EXISTS `'.preg_quote($table,'/').'`;\s*CREATE TABLE `'.preg_quote($table,'/').'`\s*\((.*?)\)\s*ENGINE=.+?;/si';
            if (preg_match($pattern, $schemaSql, $m)) {
                $body = $m[1];
                // sanitize defaults: remove curdate() and normalize quoted values
                $body = preg_replace("/DEFAULT\\s+curdate\(\)/i", '', $body);
                // normalize odd variants of DEFAULT active to a safe quoted literal
                $body = preg_replace("/DEFAULT\\s+['\"]*active['\"]*/i", "DEFAULT 'active'", $body);
                // remove escaped quotes and stray backslashes that may break SQL
                $body = str_replace("\\'", "'", $body);
                $body = str_replace('\\"', '"', $body);
                $body = str_replace('\\\\', '', $body);
                // collapse repeated single quotes into a single quote
                $body = preg_replace("/''+/", "'", $body);
                // strip non-printable/control chars
                $body = preg_replace('/[\x00-\x1F\x7F]/', '', $body);
                $create = "CREATE TABLE `{$table}` (" . $body . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            }
        }
        if ($create === '') {
            // fallback: build basic create from columns
            $cols = [];
            foreach ($meta['columns'] as $c) {
                $name = $c['Field'];
                $type = $c['Type'];
                $null = ($c['Null']==='YES') ? 'NULL' : 'NULL'; // make nullable to avoid failures
                $cols[] = "`{$name}` {$type} {$null}";
            }
            $create = "CREATE TABLE `{$table}` (" . implode(', ', $cols) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        }

        try {
            $pdo->exec($create);
            $applied[] = "created_table: {$table}";
        } catch (Exception $e) {
            fwrite(STDERR, "Failed create {$table}: " . $e->getMessage() . "\n");
            // attempt safe fallback: construct basic create from column metadata (all NULLable)
            $cols = [];
            if (!empty($meta['columns'])) {
                foreach ($meta['columns'] as $c) {
                    $name = $c['Field'];
                    $type = $c['Type'];
                    $cols[] = "`{$name}` {$type} NULL";
                }
                $fallback = "CREATE TABLE `{$table}` (" . implode(', ', $cols) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                try {
                    $pdo->exec($fallback);
                    $applied[] = "created_table_fallback: {$table}";
                } catch (Exception $e2) {
                    fwrite(STDERR, "Fallback create failed for {$table}: " . $e2->getMessage() . "\n");
                }
            }
        }
    }

    // check columns (skip if table still doesn't exist)
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    } catch (Exception $e) {
        fwrite(STDERR, "Skipping columns check for {$table}: " . $e->getMessage() . "\n");
        continue;
    }
    $localCols = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) $localCols[$r['Field']] = $r;

    foreach ($meta['columns'] as $col) {
        $name = $col['Field'];
        if (!isset($localCols[$name])) {
            $type = $col['Type'];
            // safe default: NULLABLE, no default
            $sql = "ALTER TABLE `{$table}` ADD COLUMN `{$name}` {$type} NULL";
            try {
                $pdo->exec($sql);
                $applied[] = "added_column: {$table}.{$name}";
            } catch (Exception $e) {
                fwrite(STDERR, "Failed add column {$table}.{$name}: " . $e->getMessage() . "\n");
            }
        }
    }
}

fwrite(STDOUT, "Applied actions:\n" . implode("\n", $applied) . "\n");

// re-run inspector to refresh stubs
passthru('php "' . __DIR__ . '/inspect_remote_db.php"', $rc);
fwrite(STDOUT, "Inspector exit: {$rc}\n");

return 0;
