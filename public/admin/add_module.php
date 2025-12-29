<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Crear módulo</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Crear módulo</h1>
<form method="post" action="save_module.php">
  <?= csrf_input_field() ?>
  <div><label>Page ID<br><input name="page_id" required></label></div>
  <div><label>Tipo<br><input name="type" value="html"></label></div>
  <div><label>Orden<br><input name="order" type="number" value="0"></label></div>
  <div><label>Contenido<br><textarea name="content" rows="6"></textarea></label></div>
  <div style="margin-top:10px"><button type="submit">Crear módulo</button></div>
</form>
</body></html>
