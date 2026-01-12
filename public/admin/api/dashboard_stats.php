<?php
require_once __DIR__ . '/../../../includes/config.php';
header('Content-Type: application/json; charset=utf-8');
// determine local PDO: prefer LOCAL_DB_* env settings if present
$localPdo = null; global $env;
if (!empty($env['LOCAL_DB_DATABASE'])) {
    $h = $env['LOCAL_DB_HOST'] ?? '127.0.0.1';
    $p = $env['LOCAL_DB_PORT'] ?? 3306;
    $dbn = $env['LOCAL_DB_DATABASE'];
    $u = $env['LOCAL_DB_USERNAME'] ?? ($env['DB_USERNAME'] ?? 'root');
    $pw = $env['LOCAL_DB_PASSWORD'] ?? ($env['DB_PASSWORD'] ?? '');
    try { $dsn = "mysql:host={$h};port={$p};dbname={$dbn};charset=utf8mb4"; $localPdo = new PDO($dsn,$u,$pw,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); } catch (Exception $e) { $localPdo = null; }
}
if (!$localPdo) { $localPdo = try_get_pdo(); }
$result = ['ok' => true, 'local' => [], 'remote' => [], 'mismatches' => []];

// Tables of interest
$tables = ['pages','modules','users','settings','admins'];

// local counts
if ($localPdo) {
    foreach ($tables as $t) {
        try {
            // handle installations where pages are stored in `cms_pages`
            $checkTable = $t;
            if ($t === 'pages') {
                // prefer cms_pages if present
                try { $test = $localPdo->query("SELECT 1 FROM `cms_pages` LIMIT 1"); if ($test) $checkTable = 'cms_pages'; } catch (Exception $e) { }
            }
            $stmt = $localPdo->query("SELECT COUNT(*) AS c FROM `{$checkTable}`");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $result['local'][$t] = intval($row['c'] ?? 0);
        } catch (Exception $e) {
            $result['local'][$t] = null; // table missing or error
        }
    }
} else {
    foreach ($tables as $t) { $result['local'][$t] = null; }
}

// remote schema counts (from data/remote_schema.json if available)
$remotePath = __DIR__ . '/../../data/remote_schema.json';
if (!file_exists($remotePath)) { $remotePath = __DIR__ . '/../../../data/remote_schema.json'; }
if (file_exists($remotePath)) {
    $rjson = json_decode(file_get_contents($remotePath), true);
    foreach ($tables as $t) {
        if (isset($rjson[$t]) && isset($rjson[$t]['count'])) $result['remote'][$t] = intval($rjson[$t]['count']);
        else $result['remote'][$t] = null;
    }
} else {
    foreach ($tables as $t) { $result['remote'][$t] = null; }
}

// mismatches summary
foreach ($tables as $t) {
    $l = $result['local'][$t] ?? null; $r = $result['remote'][$t] ?? null;
    if ($l === null || $r === null) { $result['mismatches'][$t] = ($l === $r) ? false : true; continue; }
    $result['mismatches'][$t] = ($l !== $r);
}

// extra: quick sample of latest backups and last migration generation
$backupsDir = __DIR__ . '/../../xlerion-backups';
$result['backups'] = [];
if (is_dir($backupsDir)) {
    $files = array_values(array_filter(scandir($backupsDir), function($f){ return !in_array($f,['.','..']); }));
    rsort($files);
    $result['backups'] = array_slice($files,0,8);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
