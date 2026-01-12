<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$pdo = try_get_pdo();
if (!$pdo) { http_response_code(500); echo "DB unavailable"; exit; }

$id = isset($_POST['id']) ? intval($_POST['id']) : null;
$table = $_POST['table'] ?? 'users';
$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$role = $_POST['role'] ?? null;
$csrf = $_POST['csrf_token'] ?? null;
if (!validate_csrf_token($csrf)) { http_response_code(403); echo 'invalid csrf'; exit; }

// determine actual table
$possible = ['users','admins'];
if (!in_array($table, $possible)) $table = 'users';

if ($id) {
    // backup existing
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ?"); $stmt->execute([$id]); $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        // write a JSON backup of the current record
        $bdir = __DIR__ . '/../../xlerion-backups'; if (!is_dir($bdir)) mkdir($bdir,0755,true);
        $bf = $bdir . '/user_' . $id . '_' . date('Ymd_His') . '.json';
        file_put_contents($bf, json_encode($row, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    }
    // update
    $fields = [];
    $params = [];
    if ($username !== null) { $fields[] = 'username = ?'; $params[] = $username; }
    if ($email !== null) { $fields[] = 'email = ?'; $params[] = $email; }
    if ($role !== null) { $fields[] = 'role = ?'; $params[] = $role; }
    if ($password) { $fields[] = 'password = ?'; $params[] = password_hash($password, PASSWORD_DEFAULT); }
    if (!empty($fields)) {
        $params[] = $id;
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql); $ok = $stmt->execute($params);
        if ($ok) { audit_log('user.update', null, ['id'=>$id,'table'=>$table]); header('Location: /public/admin/list_users.php'); exit; }
    }
    echo 'No changes or update failed';
} else {
    // create
    $pwd = password_hash($password ?: bin2hex(random_bytes(6)), PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO ' . $table . ' (username, email, password, role) VALUES (?, ?, ?, ?)');
    $ok = $stmt->execute([$username, $email, $pwd, $role]);
    if ($ok) { audit_log('user.create', null, ['username'=>$username,'table'=>$table]); header('Location: /public/admin/list_users.php'); exit; }
    echo 'Insert failed';
}
