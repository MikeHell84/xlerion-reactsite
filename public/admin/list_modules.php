<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$pdo = try_get_pdo();
if (!$pdo) { echo "<p>DB connection unavailable.</p>"; exit; }
$stmt = $pdo->query('SELECT id, page_id, type, content, `order` FROM modules ORDER BY page_id, `order` ASC');
$modules = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Módulos</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Módulos</h1>
<table border="1" cellpadding="6" cellspacing="0">
<tr><th>ID</th><th>Page ID</th><th>Type</th><th>Order</th><th>Content</th><th>Actions</th></tr>
<?php foreach ($modules as $m): ?>
  <tr>
    <td><?=htmlspecialchars($m['id'])?></td>
    <td><?=htmlspecialchars($m['page_id'])?></td>
    <td><?=htmlspecialchars($m['type'])?></td>
    <td><?=htmlspecialchars($m['order'])?></td>
    <td><?=htmlspecialchars(mb_strimwidth($m['content'], 0, 120, '...'))?></td>
    <td><a href="/public/admin/index.php?page=edit_module&id=<?=urlencode($m['id'])?>">Editar</a> |
        <a href="#" class="delete-module" data-id="<?=htmlspecialchars($m['id'])?>">Eliminar</a></td>
  </tr>
<?php endforeach; ?>
</table>
<script>
document.addEventListener('click', function(e){ if (e.target && e.target.classList && e.target.classList.contains('delete-module')){
  if (!confirm('Confirmar eliminación del módulo #' + e.target.dataset.id + '?')) return; var fd = new FormData(); fd.append('id', e.target.dataset.id); fd.append('csrf_token', window.XLERION_CSRF || ''); fetch('/api/modules.php', { method: 'DELETE', body: fd }).then(r=>r.json()).then(j=>{ if (j.ok) location.reload(); else alert('Error'); }).catch(()=>alert('Error de red')); }});
</script>
</body></html>
