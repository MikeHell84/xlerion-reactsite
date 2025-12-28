<?php
/**
 * Export remote-only rows: for each file in data/remote_data/*.json,
 * produce xlerion-backups/remote_only/<table>_remote_only.json containing rows
 * present in remote JSON but missing in local DB (by id, slug or email).
 */
require_once __DIR__ . '/../includes/config.php';

$remoteDir = __DIR__ . '/../data/remote_data';
if (!is_dir($remoteDir)) { echo "remote_data dir not found: $remoteDir\n"; exit(1); }

$pdo = try_get_pdo();
if (!$pdo) { echo "No DB connection (try_get_pdo returned null). Aborting.\n"; exit(1); }

$files = glob($remoteDir . '/*.json');
if (!$files) { echo "No JSON files in $remoteDir\n"; exit(0); }

$outDir = __DIR__ . '/../xlerion-backups/remote_only'; if (!is_dir($outDir)) mkdir($outDir, 0755, true);
$summary = [];

foreach ($files as $f) {
    $table = basename($f, '.json');
    echo "Processing: $table\n";
    $remoteRows = json_decode(file_get_contents($f), true);
    if (!is_array($remoteRows)) { echo " - invalid json, skipping\n"; continue; }

    // determine key field
    $keyField = null;
    foreach (['id','slug','email'] as $k) { if (isset($remoteRows[0][$k])) { $keyField = $k; break; } }

    // check local table existence
    try { $exists = (bool)$pdo->query("SHOW TABLES LIKE '" . addslashes($table) . "'")->fetchColumn(); }
    catch (Exception $e) { echo " - show table error: {$e->getMessage()}\n"; $summary[$table]=['error'=>$e->getMessage()]; continue; }

    if (!$exists) {
        // all remote rows are remote-only — dump them
        $outFile = $outDir . '/' . $table . '_remote_only.json';
        file_put_contents($outFile, json_encode($remoteRows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo " - table missing locally, wrote all remote rows to: $outFile\n";
        $summary[$table]=['remote_only'=>count($remoteRows),'out'=>$outFile];
        continue;
    }

    if ($keyField) {
        $remoteKeys = array_values(array_filter(array_map(function($r) use ($keyField){ return $r[$keyField] ?? null; }, $remoteRows)));
        // fetch local keys
        try { $stmt = $pdo->query("SELECT `$keyField` FROM `$table`"); $localKeys = $stmt->fetchAll(PDO::FETCH_COLUMN,0); }
        catch (Exception $e) { echo " - error reading local keys: {$e->getMessage()}\n"; $summary[$table]=['error'=>$e->getMessage()]; continue; }

        $remoteOnlyKeys = array_values(array_diff($remoteKeys, $localKeys));
        $remoteOnlyRows = array_filter($remoteRows, function($r) use ($keyField, $remoteOnlyKeys){ return isset($r[$keyField]) && in_array($r[$keyField], $remoteOnlyKeys); });
        $outFile = $outDir . '/' . $table . '_remote_only.json';
        file_put_contents($outFile, json_encode(array_values($remoteOnlyRows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo " - remote_only: " . count($remoteOnlyRows) . ", wrote to: $outFile\n";
        $summary[$table]=['remote_only'=>count($remoteOnlyRows),'out'=>$outFile];
    } else {
        // fallback: compute hash-based diff
        $remoteHashes = array_map(function($r){ return md5(json_encode($r)); }, $remoteRows);
        try { $stmt = $pdo->query("SELECT * FROM `$table`"); $local = $stmt->fetchAll(PDO::FETCH_ASSOC); }
        catch (Exception $e) { echo " - error reading local rows: {$e->getMessage()}\n"; $summary[$table]=['error'=>$e->getMessage()]; continue; }
        $localHashes = array_map(function($r){ return md5(json_encode($r)); }, $local);
        $remoteOnlyHashes = array_values(array_diff($remoteHashes, $localHashes));
        $remoteOnlyRows = [];
        foreach ($remoteRows as $i=>$r) { if (in_array(md5(json_encode($r)),$remoteOnlyHashes)) $remoteOnlyRows[] = $r; }
        $outFile = $outDir . '/' . $table . '_remote_only.json';
        file_put_contents($outFile, json_encode($remoteOnlyRows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo " - remote_only (hash): " . count($remoteOnlyRows) . ", wrote to: $outFile\n";
        $summary[$table]=['remote_only'=>count($remoteOnlyRows),'out'=>$outFile];
    }
}

$summaryFile = $outDir . '/remote_only_summary.json';
file_put_contents($summaryFile, json_encode(['generated_at'=>date('c'),'summary'=>$summary], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Done. Summary: $summaryFile\n";
audit_log('export_remote_only_rows', null, ['summary'=>$summaryFile]);
return 0;
