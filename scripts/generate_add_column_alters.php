<?php
// Generate ALTER TABLE ADD COLUMN statements for columns present in
// data/remote_schema.json but missing in the local DB. Writes output
// to migrations/add_missing_columns.sql and xlerion-backups/remote_diff_additions.sql

require_once __DIR__ . '/../includes/config.php';

echo "Loading remote schema...\n";
$jsonPath = __DIR__ . '/../data/remote_schema.json';
if (!file_exists($jsonPath)) { echo "remote_schema.json not found at $jsonPath\n"; exit(1); }
$schema = json_decode(file_get_contents($jsonPath), true);
if (!$schema) { echo "Failed to parse remote schema JSON\n"; exit(1); }

$pdo = try_get_pdo();
if (!$pdo) { echo "Could not connect to local DB via includes/config.php\n"; exit(1); }

$alterStmts = [];
$log = [];

foreach ($schema as $table => $tblinfo) {
    // Check if table exists locally
    try {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}`");
        $stmt->execute();
        $localCols = array_map(function($r){ return $r['Field']; }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (Exception $e) {
        // table doesn't exist locally
        $log[] = "TABLE_MISSING: {$table}";
        continue;
    }

    foreach ($tblinfo['columns'] as $col) {
        $field = $col['Field'];
        if (in_array($field, $localCols)) continue;
        $type = $col['Type'];
        $null = (isset($col['Null']) && strtoupper($col['Null']) === 'NO') ? 'NOT NULL' : 'NULL';
        $default = '';
        if (array_key_exists('Default', $col) && $col['Default'] !== null) {
            $d = $col['Default'];
            // handle current_timestamp() default
            if (stripos($d, 'current_timestamp') !== false) {
                $default = 'DEFAULT CURRENT_TIMESTAMP';
            } else {
                // quote default string numerics will still be quoted safely
                $default = "DEFAULT '" . str_replace("'","\\'", $d) . "'";
            }
        }
        $extra = isset($col['Extra']) && $col['Extra'] ? $col['Extra'] : '';
        $stmt = "ALTER TABLE `{$table}` ADD COLUMN `{$field}` {$type} {$null} {$default} {$extra};";
        $alterStmts[] = $stmt;
        $log[] = "ADD_COLUMN: {$table}.{$field} -- {$type} {$null} {$default} {$extra}";
    }
}

if (empty($alterStmts)) {
    echo "No missing columns detected.\n";
    exit(0);
}

$migrationsDir = __DIR__ . '/../migrations';
if (!is_dir($migrationsDir)) mkdir($migrationsDir, 0755, true);
$outFile = $migrationsDir . '/add_missing_columns.sql';
file_put_contents($outFile, "-- Generated ALTER statements to add missing columns\n-- Generated: " . date('c') . "\n\n" . implode("\n", $alterStmts) . "\n");
echo "Wrote ALTER statements to: {$outFile}\n";

$backupDir = __DIR__ . '/../xlerion-backups';
if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
$logFile = $backupDir . '/remote_diff_additions_' . date('Ymd_His') . '.log';
file_put_contents($logFile, implode("\n", $log));
echo "Wrote diff log to: {$logFile}\n";

echo "Done. Review the SQL file and apply on remote only after testing locally.\n";
