<?php
// Compare data/remote_schema.json against local DB (LOCAL_DB_*) and produce detailed migration stubs.
require_once __DIR__ . '/../includes/config.php';

$env = load_env(__DIR__ . '/../.env');
$localHost = $env['LOCAL_DB_HOST'] ?? '127.0.0.1';
$localPort = $env['LOCAL_DB_PORT'] ?? 3306;
$localDb = $env['LOCAL_DB_DATABASE'] ?? 'xlerionc_xlerion_db_test';
$localUser = $env['LOCAL_DB_USERNAME'] ?? 'root';
$localPass = $env['LOCAL_DB_PASSWORD'] ?? '';

$remoteSchemaPath = __DIR__ . '/../data/remote_schema.json';
if (!file_exists($remoteSchemaPath)) {
    fwrite(STDERR, "remote schema not found at $remoteSchemaPath\n");
    exit(1);
}
$remoteSchema = json_decode(file_get_contents($remoteSchemaPath), true);
if (!is_array($remoteSchema)) {
    fwrite(STDERR, "failed parsing remote schema json\n");
    exit(1);
}

try {
    $pdo = new PDO("mysql:host={$localHost};port={$localPort};dbname={$localDb}", $localUser, $localPass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    fwrite(STDERR, "Could not connect to local DB: " . $e->getMessage() . "\n");
    exit(1);
}

$outFile = __DIR__ . '/remote_to_local_migration_stubs.sql';
$logFile = __DIR__ . '/../logs/remote_schema_sync.log';

$stubs = "-- Detailed migration stubs generated on " . date('c') . "\n-- Review before applying on any environment.\n\n";
$summary = [];

// load the full generated schema SQL to extract CREATE TABLE blocks if needed
$schemaSql = '';
$schemaSqlPath = __DIR__ . '/../remote_schema_only_from_json.sql';
if (file_exists($schemaSqlPath)) $schemaSql = file_get_contents($schemaSqlPath);

foreach ($remoteSchema as $table => $meta) {
    $remoteCols = [];
    foreach ($meta['columns'] as $c) $remoteCols[$c['Field']] = $c;

    // check if table exists locally
    $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    $exists = (bool)$stmt->fetchColumn();
    if (!$exists) {
        $summary[$table] = ['status'=>'missing_locally'];
        // try to extract CREATE TABLE from generated SQL
        $createStmt = '';
        if ($schemaSql) {
            $pattern = '/DROP TABLE IF EXISTS `'.preg_quote($table,'/').'`;\s*CREATE TABLE `'.preg_quote($table,'/').'`\s*\((.*?)\)\s*ENGINE=.+?;/si';
            if (preg_match($pattern, $schemaSql, $m)) {
                $createStmt = "-- Table missing locally: {$table}\n" . "DROP TABLE IF EXISTS `{$table}`;\nCREATE TABLE `{$table}` (" . trim($m[1]) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";
            }
        }
        if ($createStmt === '') {
            $createStmt = "-- Table missing locally: {$table}\n-- CREATE TABLE {$table} ( ... );\n\n";
        }
        $stubs .= $createStmt;
        continue;
    }

    // table exists: compare columns
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    $localCols = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $localCols[$r['Field']] = $r;
    }

    $missingCols = [];
    $modifiedCols = [];
    foreach ($remoteCols as $colName => $colMeta) {
        if (!isset($localCols[$colName])) {
            $missingCols[$colName] = $colMeta;
        } else {
            // compare type and null/default
            $local = $localCols[$colName];
            $remoteType = strtolower($colMeta['Type']);
            $localType = strtolower($local['Type']);
            $dif = [];
            if ($remoteType !== $localType) $dif[] = "type: local={$localType} remote={$remoteType}";
            $rNull = ($colMeta['Null']==='YES') ? 'YES' : 'NO';
            $lNull = ($local['Null']==='YES') ? 'YES' : 'NO';
            if ($rNull !== $lNull) $dif[] = "null: local={$lNull} remote={$rNull}";
            $rDef = $colMeta['Default'] === null ? 'NULL' : (string)$colMeta['Default'];
            $lDef = $local['Default'] === null ? 'NULL' : (string)$local['Default'];
            if ($rDef !== $lDef) $dif[] = "default: local={$lDef} remote={$rDef}";
            if (!empty($dif)) $modifiedCols[$colName] = $dif;
        }
    }

    if (!empty($missingCols)) {
        $stubs .= "-- Table `{$table}`: add missing columns\n";
        foreach ($missingCols as $name => $cm) {
            $type = $cm['Type'];
            $null = ($cm['Null']==='YES') ? 'NULL' : 'NOT NULL';
            $default = '';
            if (isset($cm['Default']) && $cm['Default'] !== null) {
                $d = $cm['Default'];
                if (stripos($d, 'current_timestamp') !== false) $default = " DEFAULT {$d}";
                else $default = " DEFAULT '" . str_replace("'","''", $d) . "'";
            }
            $extra = $cm['Extra'] ? ' ' . $cm['Extra'] : '';
            $stubs .= "ALTER TABLE `{$table}` ADD COLUMN `{$name}` {$type} {$null}{$default}{$extra};\n";
        }
        $stubs .= "\n";
    }
    if (!empty($modifiedCols)) {
        $stubs .= "-- Table `{$table}`: column differences detected (review before apply)\n";
        foreach ($modifiedCols as $cn=>$diffs) {
            // provide MODIFY statement suggestion but commented
            $col = $remoteCols[$cn];
            $type = $col['Type'];
            $null = ($col['Null']==='YES') ? 'NULL' : 'NOT NULL';
            $default = '';
            if (isset($col['Default']) && $col['Default'] !== null) {
                $d = $col['Default'];
                if (stripos($d, 'current_timestamp') !== false) $default = " DEFAULT {$d}";
                else $default = " DEFAULT '" . str_replace("'","''", $d) . "'";
            }
            $stubs .= "-- Differences for `{$table}`.`{$cn}`: " . implode('; ', $diffs) . "\n";
            $stubs .= "-- ALTER TABLE `{$table}` MODIFY COLUMN `{$cn}` {$type} {$null}{$default};\n\n";
        }
    }

    // check indexes: simple - check for UNIQUE on remote
    $remoteIndexes = [];
    foreach ($meta['columns'] as $c) if (!empty($c['Key']) && $c['Key']==='UNI') $remoteIndexes[] = $c['Field'];
    // fetch local uniques
    $idxStmt = $pdo->query("SHOW INDEX FROM `{$table}` WHERE Non_unique = 0");
    $localUniques = [];
    while ($r = $idxStmt->fetch(PDO::FETCH_ASSOC)) {
        $localUniques[$r['Key_name']][] = $r['Column_name'];
    }
    foreach ($remoteIndexes as $col) {
        $found = false;
        foreach ($localUniques as $k=>$cols) if (in_array($col, $cols)) $found = true;
        if (!$found) {
            $stubs .= "ALTER TABLE `{$table}` ADD UNIQUE INDEX `uniq_{$col}` (`{$col}`);\n";
        }
    }
    if (!empty($remoteIndexes)) $stubs .= "\n";

    $summary[$table] = [
        'missing_columns'=>array_keys($missingCols),
        'modified_columns'=>array_keys($modifiedCols)
    ];
}

file_put_contents($outFile, $stubs);
file_put_contents($logFile, date('c') . " Generated detailed stubs.\n" . print_r($summary, true) . "\n", FILE_APPEND);
fwrite(STDOUT, "Wrote stubs to {$outFile}\n");
fwrite(STDOUT, "Summary:\n" . print_r($summary, true));

return 0;
