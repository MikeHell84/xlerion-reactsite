<?php
require_once __DIR__ . '/../../../includes/config.php';
header('Content-Type: application/json; charset=utf-8');
$pdo = try_get_pdo();
if (!$pdo) {
    http_response_code(503);
    echo json_encode(['ok'=>false,'error'=>'No DB connection']);
    exit;
}
// Some installations use `cms_pages` as the pages table. Map real table names to UI keys.
$actualTables = ['cms_pages','pages','modules','users','admins','settings'];
$aliasMap = [
    'cms_pages' => 'pages',
    'pages' => 'pages',
    'modules' => 'modules',
    'users' => 'users',
    'admins' => 'admins',
    'settings' => 'settings',
];
$result = ['ok'=>true,'tables'=>[], 'counts'=>[]];

foreach ($actualTables as $t) {
    $alias = $aliasMap[$t] ?? $t;
    // skip if we've already populated this alias (prefer cms_pages over pages)
    if (isset($result['tables'][$alias]) && $result['tables'][$alias] !== null) continue;
    try {
        // limit rows to avoid massive payloads
        $stmt = $pdo->prepare("SELECT * FROM `{$t}` ORDER BY id DESC LIMIT 200");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result['tables'][$alias] = $rows;
        $countStmt = $pdo->query("SELECT COUNT(*) AS c FROM `{$t}`");
        $c = $countStmt->fetch(PDO::FETCH_ASSOC);
        $result['counts'][$alias] = intval($c['c'] ?? 0);
    } catch (Exception $e) {
        // table missing or error — set alias to null so UI can handle gracefully
        $result['tables'][$alias] = $result['tables'][$alias] ?? null;
        $result['counts'][$alias] = $result['counts'][$alias] ?? null;
    }
}
// include recent backups
$backupDir = __DIR__ . '/../../../../xlerion-backups';
$result['backups'] = [];
if (is_dir($backupDir)) {
    $files = array_values(array_filter(scandir($backupDir), function($f){ return !in_array($f,['.','..']); }));
    usort($files, function($a,$b) use ($backupDir){ return filemtime($backupDir . DIRECTORY_SEPARATOR . $b) <=> filemtime($backupDir . DIRECTORY_SEPARATOR . $a); });
    foreach (array_slice($files,0,20) as $f) {
        $result['backups'][] = ['name'=>$f,'mtime'=>date('Y-m-d H:i:s', filemtime($backupDir . DIRECTORY_SEPARATOR . $f))];
    }
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
