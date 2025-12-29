<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Bad method']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$csrf = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
if (!validate_csrf_token($csrf)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Invalid CSRF']);
    exit;
}

if (!$id) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'Missing id']);
    exit;
}

try {
    $pdo = try_get_pdo();
    if ($pdo) {
        // backup current row or table snippet
        try {
            $stmt = $pdo->prepare('SELECT * FROM pages WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if ($row) {
                $dumpFile = __DIR__ . '/../../xlerion-backups/page_deleted_' . $id . '_' . date('Ymd_His') . '.json';
                @file_put_contents($dumpFile, json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } catch (Exception $e) {}
        $stmtDel = $pdo->prepare('DELETE FROM pages WHERE id = ?');
        $stmtDel->execute([$id]);
        audit_log('page.delete', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'message' => 'Página eliminada', 'id' => $id]);
        exit;
    } else {
        // fallback JSON
        $pagesFile = __DIR__ . '/../../data/pages.json';
        if (!file_exists($pagesFile)) {
            echo json_encode(['ok' => false, 'error' => 'No data fallback']); exit;
        }
        $data = json_decode(file_get_contents($pagesFile), true) ?: [];
        $new = [];
        $found = false;
        foreach ($data as $p) {
            if (isset($p['id']) && $p['id'] == $id) {
                $found = true; continue; // skip
            }
            $new[] = $p;
        }
        if (!$found) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'ID not found']); exit;
        }
        // backup
        backup_file($pagesFile);
        file_put_contents($pagesFile, json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        audit_log('page.delete_fallback', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'message' => 'Página eliminada (fallback)', 'id' => $id]);
        exit;
    }
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
