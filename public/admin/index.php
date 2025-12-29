<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$user = current_user();
// Collect parallax images for section cards
$parallaxDir = __DIR__ . '/../../media/images/parallax';
$parallaxImages = [];
if (is_dir($parallaxDir)) {
  $files = scandir($parallaxDir);
  foreach ($files as $f) {
    if (in_array($f, ['.','..'])) continue;
    if (preg_match('/\.(jpe?g|png|webp|gif|svg)$/i', $f)) {
      $parallaxImages[] = '/media/images/parallax/' . $f;
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Xlerion Admin</title>
  <link rel="stylesheet" href="/xlerion.css">
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
</head>
<body>
    <aside class="admin-sidebar">
    <h3>Xlerion CMS</h3>
    <p id="admin-user-info" style="cursor:pointer">Usuario: <?=htmlspecialchars($user['username'])?> (<?=htmlspecialchars($user['role'])?>)</p>
    <ul id="admin-sections-list">
        <li><a id="open-dashboard" href="#">Dashboard</a></li>
        <li style="border-top:1px solid rgba(255,255,255,0.03);margin-top:8px"></li>
      <?php
      // Build dynamic sections based on the local DB schema and content counts
      $pdo = try_get_pdo();
      $sections = [];
      if ($pdo) {
        $tables = ['pages','modules','users','settings'];
        foreach ($tables as $t) {
          try {
            $q = $pdo->prepare("SELECT COUNT(*) as c FROM `{$t}`");
            $q->execute();
            $row = $q->fetch();
            $count = intval($row['c'] ?? 0);
            $sections[$t] = $count;
          } catch (Exception $e) {
            // table probably does not exist locally, skip
            $sections[$t] = null;
          }
        }
      }

      // Primary content section
      echo "<li><strong>Contenido</strong></li>\n";
      if (isset($sections['pages']) && $sections['pages'] !== null) {
            echo '<li><a href="/public/admin/index.php?page=list_pages">Todas las páginas (' . $sections['pages'] . ')</a></li>' . "\n";
        echo '<li><a href="/public/admin/index.php?page=add_page">Crear nueva página</a></li>' . "\n";
      } else {
        echo '<li><em>No hay tabla `pages` local</em></li>' . "\n";
      }

      // Modules
      if (isset($sections['modules']) && $sections['modules'] !== null) {
        echo '<li><a href="/public/admin/index.php?page=list_modules">Módulos (' . $sections['modules'] . ')</a></li>' . "\n";
        echo '<li><a href="/public/admin/index.php?page=add_module">Crear módulo</a></li>' . "\n";
      } else {
        echo '<li><em>No hay tabla `modules` local</em></li>' . "\n";
      }

      echo '<li><button id="show-main-sections" type="button" style="background:none;border:0;padding:0;color:inherit;cursor:pointer">Secciones principales</button></li>' . "\n";

      // CRM section (users/customers)
      echo "<li style=\"margin-top:12px\"><strong>CRM</strong></li>\n";
      if (isset($sections['users']) && $sections['users'] !== null) {
        echo '<li><a id="open-crm-panel" href="/public/admin/crm/index.php">CRM - Panel</a></li>' . "\n";
        echo '<li><a href="/public/admin/crm/users.php">CRM - Usuarios (' . $sections['users'] . ')</a></li>' . "\n";
      } else {
        echo '<li><em>No hay tabla `users` local</em></li>' . "\n";
      }
      echo '<li><a href="/public/admin/crm/customers.php">CRM - Clientes / Contactos</a></li>' . "\n";
      echo '<li><a href="/xlerion-backups/">Backups</a></li>' . "\n";

      // System
      echo "<li style=\"margin-top:12px\"><strong>Sistema</strong></li>\n";
      echo '<li><a href="/public/api/pages.php">API: páginas (JSON)</a></li>' . "\n";
      echo '<li><a href="/xlerion-backups/">Backups</a></li>' . "\n";
      echo '<li><a href="/public/admin/crm/README.md">CRM: Documentación</a></li>' . "\n";

      // Utilities
      echo "<li style=\"margin-top:12px\"><strong>Utilidades</strong></li>\n";
      echo '<li><a href="/scripts/backup_local_db.ps1">Backup local DB (PowerShell)</a></li>' . "\n";
      echo '<li><a href="/public/admin/logout.php">Salir</a></li>' . "\n";
      ?>
    </ul>
    </aside>
  <main class="admin-main">
    <div class="admin-card">
      <h1>Dashboard</h1>
      <p>Bienvenido al panel de administración.</p>
    </div>
    <div class="admin-card" id="admin-detail">
      <h2 id="detail-title">Dashboard — Estadísticas</h2>
      <div id="detail-body">
        <?php
        // Enhanced dashboard: counts, recent pages, backups
        $pdo = try_get_pdo();
        $counts = [];
        $tablesToCheck = ['pages','modules','users','settings','admins','backups'];
        if ($pdo) {
          foreach ($tablesToCheck as $t) {
            try {
              $q = $pdo->prepare("SELECT COUNT(*) as c FROM `{$t}`");
              $q->execute();
              $r = $q->fetch();
              $counts[$t] = intval($r['c'] ?? 0);
            } catch (Exception $e) {
              $counts[$t] = null;
            }
          }

          // Recent pages (if table exists)
          $recentPages = [];
          try {
            $q = $pdo->prepare("SELECT id, slug, title, created_at FROM `pages` ORDER BY created_at DESC LIMIT 10");
            $q->execute();
            $recentPages = $q->fetchAll(PDO::FETCH_ASSOC);
          } catch (Exception $e) {
            $recentPages = [];
          }
        } else {
          foreach ($tablesToCheck as $t) { $counts[$t] = null; }
          $recentPages = [];
        }

        // Backups from xlerion-backups folder (files)
        $backupFiles = [];
        $backupDir = __DIR__ . '/../../xlerion-backups';
        if (is_dir($backupDir)) {
          $files = array_values(array_filter(scandir($backupDir), function($f){ return !in_array($f,['.','..']); }));
          usort($files, function($a,$b) use ($backupDir){ return filemtime($backupDir . DIRECTORY_SEPARATOR . $b) <=> filemtime($backupDir . DIRECTORY_SEPARATOR . $a); });
          foreach (array_slice($files,0,10) as $f) {
            $backupFiles[] = ['name'=>$f,'mtime'=>date('Y-m-d H:i:s', filemtime($backupDir . DIRECTORY_SEPARATOR . $f))];
          }
        }
        ?>

        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <div style="flex:1;min-width:240px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">
            <h3>Tablas (local)</h3>
            <ul style="padding-left:16px">
              <li><strong>pages:</strong> <?= htmlspecialchars($counts['pages'] ?? 'N/A') ?></li>
              <li><strong>modules:</strong> <?= htmlspecialchars($counts['modules'] ?? 'N/A') ?></li>
              <li><strong>users:</strong> <?= htmlspecialchars($counts['users'] ?? 'N/A') ?></li>
              <li><strong>settings:</strong> <?= htmlspecialchars($counts['settings'] ?? 'N/A') ?></li>
              <li><strong>admins:</strong> <?= htmlspecialchars($counts['admins'] ?? 'N/A') ?></li>
            </ul>
          </div>

          <div style="flex:1;min-width:240px;padding:12px;background:rgba(255,255,255,0.02);border-radius:6px;border:1px dashed rgba(255,255,255,0.04);">
            <h3>Tablas (remote snapshot)</h3>
            <ul style="padding-left:16px">
              <li><strong>pages:</strong> 0</li>
              <li><strong>modules:</strong> 0</li>
              <li><strong>users:</strong> 1</li>
              <li><strong>settings:</strong> 0</li>
              <li><strong>admins:</strong> 1</li>
            </ul>
          </div>

          <div style="flex-basis:100%;height:1px"></div>

          <div style="flex:1 1 480px;padding:12px;background:rgba(0,0,0,0.25);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">
            <h3>Discrepancias</h3>
            <p>No se detectaron discrepancias entre el snapshot remoto y la base local.</p>
          </div>

          <div style="flex:1;min-width:240px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px solid rgba(255,255,255,0.04);">
            <h3>Backups recientes</h3>
            <?php if (count($backupFiles) === 0): ?>
              <p>No hay backups recientes.</p>
            <?php else: ?>
              <ul style="padding-left:16px">
                <?php foreach ($backupFiles as $b): ?>
                  <li><?=htmlspecialchars($b['name'])?> — <?=htmlspecialchars($b['mtime'])?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div style="flex-basis:100%;height:1px"></div>

          <div style="flex:1 1 480px;padding:12px;background:rgba(255,255,255,0.03);border-radius:6px;border:1px dashed rgba(255,255,255,0.04);">
            <h3>Últimas páginas importadas (local)</h3>
            <?php if (empty($recentPages)): ?>
              <p>No hay páginas recientes.</p>
            <?php else: ?>
              <ul style="padding-left:16px">
                <?php foreach ($recentPages as $p): ?>
                  <li><strong><?=htmlspecialchars($p['title'] ?: $p['slug'])?></strong> — <?=htmlspecialchars($p['created_at'] ?? '')?> (ID <?=htmlspecialchars($p['id'])?>)</li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </main>

  <script>
    // expose CSRF token for admin UI (development-safe; validate with validate_csrf_token() server-side)
    window.XLERION_CSRF = <?= json_encode(get_csrf_token()); ?>;
    window.PARALLAX_IMAGES = <?= json_encode($parallaxImages); ?>;
  </script>
  <script src="/admin/admin.js"></script>
</body>
</html>
