<?php
/**
 * Import safe desde data/remote_data/*.json hacia la BD local.
 * - Hace backup de la tabla local en xlerion-backups/<table>_backup_<ts>.json
 * - Inserta filas que no existan (no hace UPDATE ni DELETE)
 * - Registra resumen en xlerion-backups/import_remote_data.log
 */
require_once __DIR__ . '/../includes/config.php';

$remoteDir = __DIR__ . '/../data/remote_data';
if (!is_dir($remoteDir)) {
    echo "remote_data dir not found: $remoteDir\n";
    exit(1);
}

$pdo = try_get_pdo();
if (!$pdo) {
    echo "No DB connection (try_get_pdo returned null). Aborting.\n";
    exit(1);
}

$files = glob($remoteDir . '/*.json');
if (!$files) { echo "No JSON files in $remoteDir\n"; exit(0); }

$log = [];
foreach ($files as $f) {
    $table = basename($f, '.json');
    echo "Processing table: $table\n";
    $logEntry = ['table'=>$table,'file'=>$f,'inserted'=>0,'skipped'=>0,'errors'=>[]];

    // check table exists locally
    try {
        $exists = (bool)$pdo->query("SHOW TABLES LIKE '" . addslashes($table) . "'")->fetchColumn();
    } catch (Exception $e) {
        $logEntry['errors'][] = 'show table error: '.$e->getMessage();
        $log[] = $logEntry; continue;
    }
    if (!$exists) {
        $logEntry['errors'][] = 'table not found locally';
        $log[] = $logEntry; echo " - table not found locally, skipping.\n"; continue;
    }

    // backup current table rows
    try {
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $backupDir = __DIR__ . '/../xlerion-backups'; if (!is_dir($backupDir)) mkdir($backupDir,0755,true);
        $ts = date('Ymd_His');
        $backupFile = $backupDir . '/' . $table . '_backup_' . $ts . '.json';
        file_put_contents($backupFile, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo " - backup saved to: $backupFile\n";
        audit_log('import.backup', null, ['table'=>$table,'backup'=>$backupFile]);
    } catch (Exception $e) {
        $logEntry['errors'][] = 'backup error: '.$e->getMessage();
        $log[] = $logEntry; echo " - backup error: {$e->getMessage()}\n"; continue;
    }

    // read remote JSON
    $data = json_decode(file_get_contents($f), true);
    if (!is_array($data)) { $logEntry['errors'][] = 'invalid json'; $log[] = $logEntry; continue; }

    // fetch local primary keys (assume 'id' when present)
    $hasId = false;
    try {
        $cols = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) { if ($c['Field'] === 'id') { $hasId = true; break; } }
    } catch (Exception $e) { /* ignore */ }

    foreach ($data as $row) {
        try {
            if ($hasId && isset($row['id'])) {
                $check = $pdo->prepare("SELECT 1 FROM `$table` WHERE id = ? LIMIT 1");
                $check->execute([$row['id']]);
                if ($check->fetchColumn()) { $logEntry['skipped']++; continue; }
            }
            // build insert
            $cols = array_keys($row);
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            $colList = '`' . implode('`,`', $cols) . '`';
            $sql = "INSERT INTO `$table` ($colList) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $params = array_values($row);
            $stmt->execute($params);
            $logEntry['inserted']++;
        } catch (Exception $e) {
            // duplicate or other error — record and continue
            $logEntry['errors'][] = 'row insert error: '.$e->getMessage();
            $logEntry['skipped']++;
            continue;
        }
    }

    echo " - inserted: {$logEntry['inserted']}, skipped: {$logEntry['skipped']}\n";
    $log[] = $logEntry;
    audit_log('import.table', null, ['table'=>$table,'inserted'=>$logEntry['inserted'],'skipped'=>$logEntry['skipped']]);
}

$summaryFile = __DIR__ . '/../xlerion-backups/import_remote_data.log';
file_put_contents($summaryFile, date('c') . "\n" . json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n", FILE_APPEND | LOCK_EX);

echo "Import finished. Summary written to: $summaryFile\n";
return 0;
