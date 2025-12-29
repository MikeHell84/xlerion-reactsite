<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$pdo = try_get_pdo();
if (!$pdo) { http_response_code(500); echo "DB unavailable"; exit; }

$id = isset($_POST['id']) ? intval($_POST['id']) : null;
$page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : null;
$type = $_POST['type'] ?? 'html';
$content = $_POST['content'] ?? '';
$order = isset($_POST['order']) ? intval($_POST['order']) : 0;
$csrf = $_POST['csrf_token'] ?? null;
if (!validate_csrf_token($csrf)) { http_response_code(403); echo 'invalid csrf'; exit; }

if ($id) {
    // update
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE id = ?'); $stmt->execute([$id]); $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) { // backup
        backup_file(__DIR__ . '/../../xlerion-backups/modules_' . $id . '.json');
        audit_log('module.backup_before_update', null, ['id'=>$id]);
    }
    $u = $pdo->prepare('UPDATE modules SET page_id = ?, type = ?, content = ?, `order` = ? WHERE id = ?');
    $ok = $u->execute([$page_id, $type, $content, $order, $id]);
    if ($ok) { audit_log('module.update', null, ['id'=>$id]); header('Location: /public/admin/list_modules.php'); exit; }
    else { echo 'Update failed'; }
} else {
    // insert
    $i = $pdo->prepare('INSERT INTO modules (page_id, type, content, `order`) VALUES (?, ?, ?, ?)');
    $ok = $i->execute([$page_id, $type, $content, $order]);
    if ($ok) { audit_log('module.create', null, ['page_id'=>$page_id]); header('Location: /public/admin/list_modules.php'); exit; }
    else { echo 'Insert failed'; }
}
