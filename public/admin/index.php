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
<body class="admin-dashboard-page">
  <aside class="admin-sidebar">
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
  <main class="admin-main">
    <div class="admin-card">
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
    <?php elseif (isset($_GET['page']) && $_GET['page'] === 'edit_page'):
      // Edit by id or slug
      $pdo = try_get_pdo();
      $page = null;
      $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
      $slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9\-\_]/i','', $_GET['slug']) : null;
      if ($pdo) {
        if ($id) {
          $stmt = $pdo->prepare('SELECT id,slug,title,content FROM pages WHERE id = ? LIMIT 1');
          $stmt->execute([$id]);
          $page = $stmt->fetch();
        } elseif ($slug) {
          $stmt = $pdo->prepare('SELECT id,slug,title,content FROM pages WHERE slug = ? LIMIT 1');
          $stmt->execute([$slug]);
          $page = $stmt->fetch();
        }
      } else {
        // Fallback to JSON file
        $pagesFile = __DIR__ . '/../../data/pages.json';
        if (file_exists($pagesFile)) {
          $data = json_decode(file_get_contents($pagesFile), true);
          foreach ($data as $p) {
            if ($id && isset($p['id']) && $p['id'] == $id) { $page = $p; break; }
            if ($slug && isset($p['slug']) && $p['slug'] === $slug) { $page = $p; break; }
          }
        }
      }

      if (!$page) {
        echo '<div style="padding:12px;background:var(--xlerion-alert-bg);border:1px solid var(--xlerion-alert-border);margin-bottom:12px">Página no encontrada. Puedes crearla con "Crear nueva página".</div>';
      } else {
    ?>
      <h2>Editar página: <?=htmlspecialchars($page['slug'])?></h2>
      <form method="post" action="/public/admin/save_page.php">
        <input type="hidden" name="id" value="<?=htmlspecialchars($page['id'])?>">
        <div><label>Slug<br><input name="slug" value="<?=htmlspecialchars($page['slug'])?>" required></label></div>
        <div><label>Title<br><input name="title" value="<?=htmlspecialchars($page['title'])?>" required></label></div>
        <div><label>Content HTML<br><textarea name="content" rows="12"><?=htmlspecialchars($page['content'])?></textarea></label></div>
        <div style="margin-top:8px"><button type="submit">Guardar cambios</button></div>
      </form>
    <?php }
    else: ?>
      <p>Bienvenido, usa el menú lateral para administrar el contenido.</p>
    <?php endif; ?>
  </main>
</body>
</html>
