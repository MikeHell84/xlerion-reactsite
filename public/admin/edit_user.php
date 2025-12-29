<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$pdo = try_get_pdo();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null; $userTable = null;
if ($pdo && $id) {
    // try common tables
    foreach (['users','admins'] as $t) {
        try { $s = $pdo->prepare("SELECT * FROM {$t} WHERE id = ?"); $s->execute([$id]); $r = $s->fetch(PDO::FETCH_ASSOC); if ($r) { $user = $r; $userTable = $t; break; } } catch (Exception $e) { }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Editar usuario</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Editar usuario #<?=htmlspecialchars($id)?></h1>
<?php if (!$user): ?><p>Usuario no encontrado.</p><?php else: ?>
<form method="post" action="save_user.php">
  <?= csrf_input_field() ?>
  <input type="hidden" name="id" value="<?=htmlspecialchars($user['id'])?>">
  <input type="hidden" name="table" value="<?=htmlspecialchars($userTable)?>">
  <div><label>Username<br><input name="username" required value="<?=htmlspecialchars($user['username'] ?? $user['name'] ?? '')?>"></label></div>
  <div><label>Email<br><input name="email" type="email" value="<?=htmlspecialchars($user['email'] ?? '')?>"></label></div>
  <div><label>New password (leave blank to keep)<br><input name="password" type="password"></label></div>
  <div><label>Role<br><input name="role" value="<?=htmlspecialchars($user['role'] ?? '')?>"></label></div>
  <div style="margin-top:10px"><button type="submit">Guardar usuario</button></div>
</form>
<?php endif; ?>
</body></html>
