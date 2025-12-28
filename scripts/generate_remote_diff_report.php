<?php
/**
 * Genera un reporte de diferencias entre data/remote_data/*.json y las tablas locales.
 * - Para cada tabla: compara por 'id' cuando exista, de lo contrario intenta comparar por 'slug' o 'email'.
 * - Produce: xlerion-backups/remote_diff_report.json y remote_diff_report.txt
 */
require_once __DIR__ . '/../includes/config.php';

$remoteDir = __DIR__ . '/../data/remote_data';
if (!is_dir($remoteDir)) { echo "remote_data dir not found: $remoteDir\n"; exit(1); }

$pdo = try_get_pdo();
if (!$pdo) { echo "No DB connection (try_get_pdo returned null). Aborting.\n"; exit(1); }

$files = glob($remoteDir . '/*.json');
if (!$files) { echo "No JSON files in $remoteDir\n"; exit(0); }

$report = ['generated_at'=>date('c'),'tables'=>[]];

foreach ($files as $f) {
    $table = basename($f, '.json');
    $data = json_decode(file_get_contents($f), true);
    if (!is_array($data)) { $report['tables'][$table] = ['error'=>'invalid json']; continue; }
    $entry = ['remote_count'=>count($data),'local_count'=>0,'remote_only_ids'=>[],'local_only_ids'=>[],'note'=>''];

    // check local table
    try { $exists = (bool)$pdo->query("SHOW TABLES LIKE '" . addslashes($table) . "'")->fetchColumn(); }
    catch (Exception $e) { $report['tables'][$table]=['error'=>$e->getMessage()]; continue; }
    if (!$exists) { $entry['note']='table missing locally'; $entry['remote_sample']=array_slice($data,0,5); $report['tables'][$table]=$entry; continue; }

    // determine key field: prefer id, then slug, then email
    $keyField = null; foreach (['id','slug','email'] as $k) { if (isset($data[0][$k])) { $keyField=$k; break; } }
    if (!$keyField) { // fallback to hash of row
        $remoteKeys = array_map(function($r){ return md5(json_encode($r)); }, $data);
        $localKeys = [];
        // fetch all local rows and compute hashes
        $stmt = $pdo->query("SELECT * FROM `$table`"); $locals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($locals as $lr) $localKeys[] = md5(json_encode($lr));
        $entry['local_count']=count($locals);
        $remoteOnly = array_diff($remoteKeys,$localKeys);
        $localOnly = array_diff($localKeys,$remoteKeys);
        $entry['remote_only_count']=count($remoteOnly);
        $entry['local_only_count']=count($localOnly);
        $report['tables'][$table]=$entry; continue;
    }

    // collect remote keys
    $remoteKeys = [];
    foreach ($data as $r) { if (isset($r[$keyField])) $remoteKeys[] = $r[$keyField]; }

    // collect local keys
    try {
        $stmt = $pdo->query("SELECT `$keyField` FROM `$table`"); $locals = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
    } catch (Exception $e) { $entry['error']=$e->getMessage(); $report['tables'][$table]=$entry; continue; }
    $entry['local_count']=count($locals);

    $remoteOnly = array_values(array_diff($remoteKeys,$locals));
    $localOnly = array_values(array_diff($locals,$remoteKeys));
    $entry['remote_only_count']=count($remoteOnly);
    $entry['local_only_count']=count($localOnly);
    $entry['remote_only_sample']=array_slice($remoteOnly,0,10);
    $entry['local_only_sample']=array_slice($localOnly,0,10);

    $report['tables'][$table]=$entry;
}

$jsonOut = __DIR__ . '/../xlerion-backups/remote_diff_report.json';
file_put_contents($jsonOut, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$txtOut = __DIR__ . '/../xlerion-backups/remote_diff_report.txt';
$txt = "Remote diff report generated at " . date('c') . "\n\n";
foreach ($report['tables'] as $t=>$e) {
    $txt .= "Table: $t\n";
    if (isset($e['error'])) { $txt .= "  ERROR: " . $e['error'] . "\n\n"; continue; }
    $txt .= "  remote_count: " . ($e['remote_count'] ?? 0) . "\n";
    $txt .= "  local_count: " . ($e['local_count'] ?? 0) . "\n";
    $txt .= "  remote_only_count: " . ($e['remote_only_count'] ?? 0) . "\n";
    $txt .= "  local_only_count: " . ($e['local_only_count'] ?? 0) . "\n";
    if (!empty($e['remote_only_sample'])) $txt .= "  remote_only_sample: " . json_encode($e['remote_only_sample']) . "\n";
    if (!empty($e['local_only_sample'])) $txt .= "  local_only_sample: " . json_encode($e['local_only_sample']) . "\n";
    if (!empty($e['note'])) $txt .= "  note: " . $e['note'] . "\n";
    $txt .= "\n";
}
file_put_contents($txtOut, $txt);

echo "Generated report: $jsonOut and $txtOut\n";
audit_log('generate_remote_diff_report', null, ['json'=>$jsonOut,'txt'=>$txtOut]);
return 0;
