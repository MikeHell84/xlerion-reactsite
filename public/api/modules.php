<?php
require_once __DIR__ . '/../../includes/config.php';
$pdo = try_get_pdo();
// If DB not available, return JSON error (avoid fatal call on null $pdo)
if ($pdo === null) {
    // DB driver missing or connection failed (e.g. pdo_mysql not enabled).
    error_log('[modules.php] DB connection failed: try_get_pdo returned null');
    // Fallback: return empty modules list so admin UI doesn't break in development.
    echo json_encode(['ok' => true, 'modules' => []]);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page_id = isset($_GET['page_id']) ? intval($_GET['page_id']) : null;
    if ($page_id) {
        $stmt = $pdo->prepare('SELECT * FROM modules WHERE page_id = ? ORDER BY `order` ASC');
        $stmt->execute([$page_id]);
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query('SELECT * FROM modules ORDER BY `order` ASC');
        $modules = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    echo json_encode(['ok' => true, 'modules' => $modules]);
    exit;
}

if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) { require_login(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_id = $_POST['page_id'] ?? null;
    $type = $_POST['type'] ?? 'html';
    $content = $_POST['content'] ?? '';
    $order = intval($_POST['order'] ?? 0);
    $csrf = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
    if (!validate_csrf_token($csrf)) { http_response_code(403); echo json_encode(['ok' => false, 'error' => 'invalid csrf']); exit; }
    if ($page_id === null) { http_response_code(400); echo json_encode(['ok' => false, 'error' => 'page_id required']); exit; }
    $stmt = $pdo->prepare('INSERT INTO modules (page_id, type, content, `order`) VALUES (?, ?, ?, ?)');
    $ok = $stmt->execute([$page_id, $type, $content, $order]);
    if ($ok) { audit_log('module.create', ['page_id' => $page_id, 'type' => $type]); echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]); }
    else { http_response_code(500); echo json_encode(['ok' => false, 'error' => 'insert failed']); }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents('php://input'), $input);
    $id = $input['id'] ?? null; $csrf = $input['csrf_token'] ?? null;
    if (!validate_csrf_token($csrf)) { http_response_code(403); echo json_encode(['ok' => false, 'error' => 'invalid csrf']); exit; }
    if (!$id) { http_response_code(400); echo json_encode(['ok' => false, 'error' => 'id required']); exit; }
    $type = $input['type'] ?? null; $content = $input['content'] ?? null; $order = isset($input['order']) ? intval($input['order']) : null;
    $fields = []; $params = [];
    if ($type !== null) { $fields[] = 'type = ?'; $params[] = $type; }
    if ($content !== null) { $fields[] = 'content = ?'; $params[] = $content; }
    if ($order !== null) { $fields[] = '`order` = ?'; $params[] = $order; }
    if (empty($fields)) { echo json_encode(['ok' => false, 'error' => 'no fields to update']); exit; }
    $params[] = $id;
    $sql = 'UPDATE modules SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $stmt = $pdo->prepare($sql); $ok = $stmt->execute($params);
    if ($ok) { audit_log('module.update', ['id' => $id]); }
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents('php://input'), $input);
    $id = $input['id'] ?? null; $csrf = $input['csrf_token'] ?? null;
    if (!validate_csrf_token($csrf)) { http_response_code(403); echo json_encode(['ok' => false, 'error' => 'invalid csrf']); exit; }
    if (!$id) { http_response_code(400); echo json_encode(['ok' => false, 'error' => 'id required']); exit; }
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE id = ?'); $stmt->execute([$id]); $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) { backup_file(json_encode($row), 'module_delete_' . $id . '.json'); }
    $stmt = $pdo->prepare('DELETE FROM modules WHERE id = ?'); $ok = $stmt->execute([$id]);
    if ($ok) { audit_log('module.delete', ['id' => $id]); }
    echo json_encode(['ok' => (bool)$ok]);
    exit;
}

http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'method not allowed']);