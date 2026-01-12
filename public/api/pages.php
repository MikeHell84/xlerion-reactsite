<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

$pdo = try_get_pdo();
if ($pdo) {
    try {
        $stmt = $pdo->query('SELECT id, slug, title, content FROM pages ORDER BY id');
        $pages = $stmt->fetchAll();
        $stmtM = $pdo->prepare('SELECT id, page_id, type, content, `order` FROM modules WHERE page_id = ? ORDER BY `order` ASC, id ASC');
        foreach ($pages as &$p) {
            $stmtM->execute([$p['id']]);
            $p['modules'] = $stmtM->fetchAll();
        }
        echo json_encode(['ok' => true, 'pages' => $pages], JSON_UNESCAPED_UNICODE);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Fallback: read from data/pages.json if DB is unavailable
$fallback = __DIR__ . '/../../data/pages.json';
if (file_exists($fallback)) {
    $json = file_get_contents($fallback);
    $pages = json_decode($json, true);
    echo json_encode(['ok' => true, 'pages' => $pages], JSON_UNESCAPED_UNICODE);
    exit;
}

// No data available
http_response_code(503);
echo json_encode(['ok' => false, 'error' => 'No DB connection and no fallback data available']);

