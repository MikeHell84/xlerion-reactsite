<?php
/**
 * inspect_remote_db.php
 *
 * Read-only inspector for a remote MySQL database defined by .env (used by includes/config.php).
 * - Dumps schema (SHOW TABLES, SHOW COLUMNS) into data/remote_schema.json
 * - Dumps sample rows into data/remote_data/<table>.json
 * - Generates PHP model classes into includes/remote_models/
 * - Produces migration stubs (commented) if a local DB connection is configured via LOCAL_DB_* env vars
 *
 * IMPORTANT: This script never alters any database. It only reads and writes files under the repository.
 */

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../includes/config.php';

function safe_write_json($path, $data) {
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (file_exists($path)) {
        copy($path, $path . '.bak');
    }
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$logPath = __DIR__ . '/../logs/remote_schema_sync.log';
if (!is_dir(dirname($logPath))) mkdir(dirname($logPath), 0755, true);
function log_msg($s) {
    global $logPath;
    $line = date('c') . ' ' . $s . "\n";
    file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
    echo $line;
}

// Connect to remote DB (uses .env via includes/config.php)
try {
    $remotePdo = get_pdo();
} catch (Exception $e) {
    log_msg('[ERROR] Could not connect to remote DB: ' . $e->getMessage());
    exit(1);
}

log_msg('Connected to remote DB. Reading tables...');

$tables = [];
$stmt = $remotePdo->query('SHOW TABLES');
if ($stmt) {
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if (empty($tables)) {
    log_msg('[WARN] No tables found in remote DB');
}

$schema = [];
foreach ($tables as $table) {
    log_msg("Inspecting table: $table");
    $colsStmt = $remotePdo->query("SHOW COLUMNS FROM `{$table}`");
    $columns = $colsStmt ? $colsStmt->fetchAll(PDO::FETCH_ASSOC) : [];
    $count = 0;
    try {
        $cstmt = $remotePdo->query("SELECT COUNT(*) as c FROM `{$table}`");
        $count = $cstmt ? intval($cstmt->fetchColumn()) : 0;
    } catch (Exception $e) {
        log_msg("[WARN] COUNT failed for $table: " . $e->getMessage());
    }
    $sample = [];
    try {
        $sstmt = $remotePdo->query("SELECT * FROM `{$table}` LIMIT 20");
        $sample = $sstmt ? $sstmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (Exception $e) {
        log_msg("[WARN] SELECT sample failed for $table: " . $e->getMessage());
    }
    $schema[$table] = [
        'columns' => $columns,
        'count' => $count,
        'sample_path' => "data/remote_data/{$table}.json",
    ];
    // save sample
    safe_write_json(__DIR__ . "/../data/remote_data/{$table}.json", $sample);

    // generate PHP model class
    $className = preg_replace('/[^A-Za-z0-9]/', '_', ucfirst($table)) . 'Model';
    $modelDir = __DIR__ . '/../includes/remote_models';
    if (!is_dir($modelDir)) mkdir($modelDir, 0755, true);
    $props = '';
    foreach ($columns as $col) {
        $colName = $col['Field'];
        // build property declaration without interpolating variables to avoid runtime warnings
        $props .= '    public $' . $colName . ";\n";
    }
    $modelCode = "<?php\nnamespace Remote\\Models;\n\nclass {$className} {\n{$props}\n}\n";
    $modelPath = $modelDir . '/' . $className . '.php';
    if (!file_exists($modelPath)) {
        file_put_contents($modelPath, $modelCode);
        log_msg("Wrote model: includes/remote_models/{$className}.php");
    } else {
        log_msg("Model exists, skipped: includes/remote_models/{$className}.php");
    }
}

// Save overall schema
safe_write_json(__DIR__ . '/../data/remote_schema.json', $schema);
log_msg('Saved remote_schema.json and table samples.');

// Optional: compare with local DB if LOCAL_DB_* vars exist in .env
global $env;
$localConfigured = !empty($env['LOCAL_DB_HOST']) && !empty($env['LOCAL_DB_DATABASE']);
if ($localConfigured) {
    log_msg('LOCAL_DB_* found in .env — attempting local DB comparison (read-only).');
    $localHost = $env['LOCAL_DB_HOST'];
    $localPort = $env['LOCAL_DB_PORT'] ?? 3306;
    $localDb = $env['LOCAL_DB_DATABASE'];
    $localUser = $env['LOCAL_DB_USERNAME'] ?? $env['LOCAL_DB_USER'] ?? '';
    $localPass = $env['LOCAL_DB_PASSWORD'] ?? $env['LOCAL_DB_PASS'] ?? '';
    try {
        $dsn = "mysql:host={$localHost};port={$localPort};dbname={$localDb};charset=utf8mb4";
        $localPdo = new PDO($dsn, $localUser, $localPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } catch (Exception $e) {
        log_msg('[ERROR] Could not connect to local DB: ' . $e->getMessage());
        $localPdo = null;
    }
    if ($localPdo) {
        $migrationStubs = [];
        foreach ($schema as $table => $info) {
            // check if table exists locally
            $exists = false;
            try {
                $r = $localPdo->prepare('SHOW TABLES LIKE ?');
                $r->execute([$table]);
                $exists = (bool)$r->fetchColumn();
            } catch (Exception $e) {
                log_msg("[WARN] checking local table $table: " . $e->getMessage());
            }
            if (!$exists) {
                $migrationStubs[] = "-- Table missing locally: {$table} (create manually after review)\n-- CREATE TABLE {$table} ( ... ) ;\n";
                continue;
            }
            // compare columns
            $localCols = [];
            try {
                $cstmt = $localPdo->query("SHOW COLUMNS FROM `{$table}`");
                $localCols = $cstmt ? $cstmt->fetchAll(PDO::FETCH_COLUMN, 0) : [];
            } catch (Exception $e) {
                log_msg("[WARN] local SHOW COLUMNS failed for $table: " . $e->getMessage());
            }
            $remoteCols = array_map(function($c){ return $c['Field']; }, $info['columns']);
            $diff = array_diff($remoteCols, $localCols);
            if (!empty($diff)) {
                log_msg("Columns in remote but missing locally for table $table: " . implode(', ', $diff));
                foreach ($diff as $missing) {
                    // find remote column definition
                    foreach ($info['columns'] as $rc) {
                        if ($rc['Field'] === $missing) {
                            $type = $rc['Type'];
                            $null = ($rc['Null'] === 'YES') ? 'NULL' : 'NOT NULL';
                            $def = isset($rc['Default']) ? "DEFAULT '" . $rc['Default'] . "'" : '';
                            $migrationStubs[] = "ALTER TABLE `{$table}` ADD COLUMN `{$missing}` {$type} {$null} {$def};";
                        }
                    }
                }
            }
        }
        if (!empty($migrationStubs)) {
            $stubPath = __DIR__ . '/remote_to_local_migration_stubs.sql';
            file_put_contents($stubPath, "-- Migration stubs generated by scripts/inspect_remote_db.php\n-- Review before applying.\n\n" . implode("\n", $migrationStubs));
            log_msg("Wrote migration stubs: {$stubPath}");
        } else {
            log_msg('No column differences detected or migration stubs empty.');
        }
    }
} else {
    log_msg('LOCAL_DB_* not present in .env — skipping local comparison. To enable, set LOCAL_DB_HOST, LOCAL_DB_DATABASE, LOCAL_DB_USERNAME, LOCAL_DB_PASSWORD.');
}

log_msg('Done.');
