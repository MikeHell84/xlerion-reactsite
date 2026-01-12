<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Crear usuario</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Crear usuario</h1>
<form method="post" action="save_user.php">
  <?= csrf_input_field() ?>
  <div><label>Username<br><input name="username" required></label></div>
  <div><label>Email<br><input name="email" type="email"></label></div>
  <div><label>Password<br><input name="password" type="password" required></label></div>
  <div><label>Role<br><input name="role" value="editor"></label></div>
  <div style="margin-top:10px"><button type="submit">Crear usuario</button></div>
</form>
</body></html>
