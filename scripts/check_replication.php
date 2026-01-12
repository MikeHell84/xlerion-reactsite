<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$pdo = try_get_pdo();
if (!$pdo) {
    echo json_encode(['ok' => false, 'error' => 'No DB connection']);
    exit(0);
}

$tables = ['cms_pages', 'pages'];
$out = [];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM `" . $t . "`");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
    } catch (Exception $e) {
        $count = null;
    }
    $rows = [];
    if ($count > 0) {
        try {
            $q = $pdo->prepare("SELECT * FROM `" . $t . "` ORDER BY id DESC LIMIT 11");
            $q->execute();
            $rows = $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $rows = [];
        }
    }
    $out[$t] = ['count' => $count, 'rows' => $rows];
}

echo json_encode(['ok' => true, 'data' => $out], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
