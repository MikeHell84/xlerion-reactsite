<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$pdo = try_get_pdo();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$module = null;
if ($pdo && $id) {
    $stmt = $pdo->prepare('SELECT * FROM modules WHERE id = ?');
    $stmt->execute([$id]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Editar módulo</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Editar módulo #<?=htmlspecialchars($id)?></h1>
<?php if (!$module): ?><p>Módulo no encontrado.</p><?php else: ?>
<form method="post" action="save_module.php">
  <?= csrf_input_field() ?>
  <input type="hidden" name="id" value="<?=htmlspecialchars($module['id'])?>">
  <div><label>Page ID<br><input name="page_id" required value="<?=htmlspecialchars($module['page_id'])?>"></label></div>
  <div><label>Tipo<br><input name="type" value="<?=htmlspecialchars($module['type'])?>"></label></div>
  <div><label>Orden<br><input name="order" type="number" value="<?=htmlspecialchars($module['order'])?>"></label></div>
  <div><label>Contenido<br><textarea name="content" rows="8"><?=htmlspecialchars($module['content'])?></textarea></label></div>
  <div style="margin-top:10px"><button type="submit">Guardar cambios</button></div>
</form>
<?php endif; ?>
</body></html>
