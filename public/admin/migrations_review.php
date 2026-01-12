<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();

// Run the generator script and capture output
$cmd = 'php ' . escapeshellarg(__DIR__ . '/../../scripts/generate_add_column_alters.php');
$out = null; $rc = null; exec($cmd . ' 2>&1', $out, $rc);

$sqlPath = __DIR__ . '/../../migrations/add_missing_columns.sql';
$sql = file_exists($sqlPath) ? file_get_contents($sqlPath) : null;

?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Review migrations</title><link rel="stylesheet" href="/xlerion.css"></head><body>
<a href="/public/admin/index.php">← Volver</a>
<h1>Migration review (generated)</h1>
<h2>Generator output</h2>
<pre style="background:#111;color:#ddd;padding:12px;border-radius:6px;max-height:240px;overflow:auto"><?=htmlspecialchars(implode("\n", $out))?></pre>

<?php if ($sql): ?>
  <h2>Generated SQL</h2>
  <form method="post" action="download_migration.php">
    <?= csrf_input_field() ?>
    <p>Review the SQL below. This file was generated locally and will not be applied automatically to remote.</p>
    <textarea name="sql" rows="20" style="width:100%;font-family:monospace"><?=htmlspecialchars($sql)?></textarea>
    <div style="margin-top:8px"><button type="submit">Download SQL file</button></div>
  </form>
<?php else: ?>
  <p>No SQL generated (no missing columns detected).</p>
<?php endif; ?>

</body></html>
