<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$pdo = try_get_pdo();
if (!$pdo) { echo "<p>DB connection unavailable.</p>"; exit; }
// Try common users table names
$tables = ['users','admins']; $users = [];
foreach ($tables as $t) {
    try { $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM {$t} ORDER BY id DESC LIMIT 200"); $res = $stmt->fetchAll(PDO::FETCH_ASSOC); if ($res) { $users = $res; break; } } catch (Exception $e) { continue; }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Usuarios</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Usuarios</h1>
<?php if (empty($users)): ?>
  <p>No se encontraron usuarios en local.</p>
<?php else: ?>
  <table border="1" cellpadding="6" cellspacing="0">
    <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?=htmlspecialchars($u['id'])?></td>
        <td><?=htmlspecialchars($u['username'] ?? $u['name'] ?? '')?></td>
        <td><?=htmlspecialchars($u['email'] ?? '')?></td>
        <td><?=htmlspecialchars($u['role'] ?? '')?></td>
        <td><?=htmlspecialchars($u['created_at'] ?? '')?></td>
        <td><a href="#">Editar</a></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
</body></html>
