<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link href="/xlerion.css" rel="stylesheet">
</head>
<body>
  <aside style="width:220px;float:left;padding:20px;background:var(--xlerion-surface);min-height:100vh;">
    <h3>Xlerion CMS</h3>
    <p>Usuario: <?=htmlspecialchars($user['username'])?> (<?=htmlspecialchars($user['role'])?>)</p>
    <ul>
      <li><a href="/public/admin/index.php?page=list_pages">Todas las páginas</a></li>
      <li><a href="/public/admin/index.php?page=add_page">Crear nueva página</a></li>
      <li><strong>Secciones principales</strong></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=inicio">Inicio</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=filosofia">Filosofía</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=soluciones">Soluciones</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=proyectos">Proyectos</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=documentacion">Documentación</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=fundador">Fundador</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=convocatorias">Convocatorias</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=contacto">Contacto</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=blog">Blog</a></li>
      <li><a href="/public/admin/index.php?page=edit_page&slug=legal">Legal</a></li>
      <li style="margin-top:12px"><a href="/public/api/pages.php">API: páginas (JSON)</a></li>
      <li><a href="/public/admin/logout.php">Salir</a></li>
    </ul>
  </aside>
  <main style="margin-left:240px;padding:20px;">
    <h1>Dashboard</h1>
    <?php if (isset($_GET['page']) && $_GET['page'] === 'list_pages'):
      $pdo = try_get_pdo();
      if (!$pdo) {
        echo '<div style="padding:12px;background:var(--xlerion-alert-bg);border:1px solid var(--xlerion-alert-border);margin-bottom:12px">No hay conexión a la base de datos. El CMS está en modo de solo-lectura o necesita configuración en <code>.env</code>.</div>';
        $rows = [];
      } else {
        $rows = $pdo->query('SELECT id,slug,title,created_at FROM pages ORDER BY id DESC')->fetchAll();
      }
    ?>
      <h2>Páginas</h2>
      <table border="1" cellpadding="6" cellspacing="0">
        <tr><th>ID</th><th>Slug</th><th>Title</th><th>Acciones</th></tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['id'])?></td>
            <td><?=htmlspecialchars($r['slug'])?></td>
            <td><?=htmlspecialchars($r['title'])?></td>
            <td><a href="/public/admin/index.php?page=edit_page&id=<?=$r['id']?>">Editar</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php elseif (isset($_GET['page']) && $_GET['page'] === 'add_page'): ?>
      <h2>Crear página</h2>
      <form method="post" action="/public/admin/save_page.php">
        <div><label>Slug<br><input name="slug" required></label></div>
        <div><label>Title<br><input name="title" required></label></div>
        <div><label>Content HTML<br><textarea name="content" rows="10"></textarea></label></div>
        <div style="margin-top:8px"><button type="submit">Guardar</button></div>
      </form>
    <?php else: ?>
      <p>Bienvenido, usa el menú lateral para administrar el contenido.</p>
    <?php endif; ?>
  </main>
</body>
</html>
