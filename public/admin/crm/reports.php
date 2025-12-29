<?php
require_once __DIR__ . '/../../../includes/header.php';

// Paths to artifacts
$backupsDir = __DIR__ . '/../../../../xlerion-backups';
$diffJson = $backupsDir . '/remote_diff_report.json';
$remoteOnlyDir = $backupsDir . '/remote_only';
$adminIndex = __DIR__ . '/../index.php';
$adminJs = __DIR__ . '/../admin.js';

function safe_read_json($path) {
  if (!file_exists($path)) return null;
  $txt = file_get_contents($path);
  $j = json_decode($txt, true);
  return $j;
}

$report = safe_read_json($diffJson);
$menuAudit = [];
// Simple audit: find ids in admin index and check admin.js contains references
if (file_exists($adminIndex)) {
  $idx = file_get_contents($adminIndex);
  preg_match_all('/id\s*=\s*"([a-zA-Z0-9_\-]+)"/i', $idx, $m);
  $ids = array_values(array_unique($m[1]));
} else { $ids = []; }

$adminJsContent = file_exists($adminJs) ? file_get_contents($adminJs) : '';
foreach ($ids as $id) {
  $found = false;
  // check for getElementById or querySelector or addEventListener referencing the id
  if (strpos($adminJsContent, "getElementById('$id')") !== false || strpos($adminJsContent, "getElementById(\"$id\"") !== false) $found = true;
  if (strpos($adminJsContent, "getElementById(\"$id\")") !== false) $found = true;
  if (strpos($adminJsContent, "document.getElementById('$id')") !== false) $found = true;
  if (strpos($adminJsContent, "document.getElementById(\"$id\")") !== false) $found = true;
  if (strpos($adminJsContent, $id) !== false && $found) {
    $status = 'handler found in admin.js';
  } else if (strpos($adminJsContent, $id) !== false) {
    $status = 'id referenced in admin.js (check usage)';
  } else {
    $status = 'no handler found in admin.js';
  }
  $menuAudit[] = ['id' => $id, 'status' => $status];
}

// Helper to show sample rows for a given table (first 10)
function sample_for_table($table) {
  $d = __DIR__ . '/../../../../xlerion-backups/remote_only/' . $table . '_remote_only.json';
  if (!file_exists($d)) return null;
  $j = json_decode(file_get_contents($d), true);
  if (!is_array($j)) return null;
  return array_slice($j, 0, 20);
}

?>
<main style="padding:24px;">
  <h1>Reportes y métricas</h1>
  <p>Reporte comparativo entre base remota y local (<code>remote_diff_report.json</code>).</p>

  <?php if ($report && isset($report['tables'])): ?>
    <table border="1" cellpadding="6" style="width:100%;border-collapse:collapse;margin-bottom:12px;background:rgba(255,255,255,0.02);">
      <thead>
        <tr><th>Tabla</th><th>Remote Count</th><th>Local Count</th><th>Remote Only</th><th>Local Only</th><th>Acciones</th></tr>
      </thead>
      <tbody>
      <?php foreach ($report['tables'] as $t => $info):
          $remote = isset($info['remote_count']) ? $info['remote_count'] : ($info['remote'] ?? 0);
          $local = isset($info['local_count']) ? $info['local_count'] : ($info['local'] ?? 0);
          $rOnly = isset($info['remote_only_count']) ? $info['remote_only_count'] : ($info['remote_only'] ?? 0);
          $lOnly = isset($info['local_only_count']) ? $info['local_only_count'] : ($info['local_only'] ?? 0);
      ?>
        <tr>
          <td><?php echo htmlspecialchars($t); ?></td>
          <td><?php echo (int)$remote; ?></td>
          <td><?php echo (int)$local; ?></td>
          <td><?php echo (int)$rOnly; ?></td>
          <td><?php echo (int)$lOnly; ?></td>
          <td>
            <?php if ($rOnly>0): ?>
              <button class="view-sample" data-table="<?php echo htmlspecialchars($t); ?>">Ver muestras</button>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No se encontró <code>remote_diff_report.json</code> en <code>/xlerion-backups/</code>.</p>
  <?php endif; ?>

  <section style="margin-top:18px">
    <h2>Auditoría rápida del menú del admin</h2>
    <p>Lista de IDs encontrados en <code>public/admin/index.php</code> y si <code>admin.js</code> referencia handlers.</p>
    <table border="1" cellpadding="6" style="border-collapse:collapse;background:rgba(255,255,255,0.02);">
      <thead><tr><th>ID</th><th>Estado</th></tr></thead>
      <tbody>
        <?php foreach ($menuAudit as $m): ?>
          <tr><td><?php echo htmlspecialchars($m['id']); ?></td><td><?php echo htmlspecialchars($m['status']); ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p style="margin-top:8px">Notas: si ves "no handler found in admin.js" el elemento puede ser un enlace estático que navega por sí mismo; si quieres, puedo añadir handlers que abran secciones dentro del panel en lugar de navegar.</p>
  </section>

  <div id="sample-modal" style="display:none;margin-top:12px;padding:12px;background:rgba(0,0,0,0.6);border-radius:6px;">
    <h3>Muestras</h3>
    <pre id="sample-content" style="white-space:pre-wrap;max-height:360px;overflow:auto;background:#0b0b0b;padding:12px;border-radius:6px;color:#fff"></pre>
    <p><button id="close-sample">Cerrar</button></p>
  </div>

  <script>
    document.querySelectorAll('.view-sample').forEach(function(btn){
      btn.addEventListener('click', function(){
        var table = this.dataset.table;
        var url = '/xlerion-backups/remote_only/' + encodeURIComponent(table) + '_remote_only.json';
        fetch(url).then(function(r){ if (!r.ok) throw new Error('No file'); return r.json(); }).then(function(j){ var s = JSON.stringify(j.slice(0,20), null, 2); document.getElementById('sample-content').textContent = s; document.getElementById('sample-modal').style.display='block'; }).catch(function(e){ document.getElementById('sample-content').textContent = 'No hay muestras disponibles.'; document.getElementById('sample-modal').style.display='block'; });
      });
    });
    document.getElementById('close-sample').addEventListener('click', function(){ document.getElementById('sample-modal').style.display='none'; });
  </script>

</main>
<?php require_once __DIR__ . '/../../../includes/footer.php';
