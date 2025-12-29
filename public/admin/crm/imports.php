<?php
require_once __DIR__ . '/../../../includes/header.php';
?>
<main style="padding:24px;">
  <h1>CRM - Imports y Backups</h1>
  <p>Herramientas y registros relacionados con la importación segura desde la fuente remota.</p>
  <ul>
    <li><a href="/xlerion-backups/import_remote_data.log" target="_blank">Ver log de importaciones</a></li>
    <li><a href="/xlerion-backups/remote_only/">Ver dumps: filas remotas faltantes</a></li>
    <li><a href="/xlerion-backups/remote_diff_report.txt" target="_blank">Ver reporte diff (remote vs local)</a></li>
    <li><a href="/xlerion-backups/">Explorar backups</a></li>
  </ul>

  <section style="margin-top:18px">
    <h2>Acciones seguras</h2>
    <p>Las operaciones destructivas no están expuestas desde la UI. Para correr el import seguro, ejecútalo en la terminal del servidor:</p>
    <pre>php scripts/import_remote_data.php</pre>
    <p>Esto crea backups en <code>/xlerion-backups/</code> antes de insertar filas que no existan localmente.</p>
  </section>
</main>

<?php require_once __DIR__ . '/../../../includes/footer.php';
