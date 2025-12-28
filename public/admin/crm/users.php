<?php
require_once __DIR__ . '/../../../includes/config.php';
require_login();
// simple users list + editable fields (adds remote fields if present locally)
$pdo = try_get_pdo();
if (!$pdo) {
    echo "<p>DB no disponible. Conexión requerida para administrar usuarios.</p>"; exit;
}

// handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id'] ?? 0);
  $csrf = $_POST['csrf_token'] ?? null;
  if (!validate_csrf_token($csrf)) { echo 'Invalid CSRF'; exit; }

  $current = current_user();
  $isAdminUser = (!empty($current['role']) && $current['role'] === 'admin') || (!empty($current['is_admin']));

  $fields = [];
  $params = [];
  // allowed candidate fields
  $allowed = ['name','email','password','totp_secret','two_factor_enabled','last_login_at','is_admin','role','reset_token','reset_token_expires'];

  // server-side protection: non-admins may not change `is_admin` or `role`
  if (!$isAdminUser) {
    // remove sensitive fields from allowed set
    $sensitive = ['is_admin','role'];
    foreach ($sensitive as $s) {
      if (isset($_POST[$s])) {
        audit_log('user.update_blocked', $_SESSION['user']['id'] ?? null, ['target_id'=>$id,'field'=>$s,'attempt_value'=>$_POST[$s]]);
      }
    }
    $allowed = array_diff($allowed, $sensitive);
  }

  $errors = [];
  foreach ($allowed as $f) {
    if (isset($_POST[$f])) {
      $raw = $_POST[$f];
      // basic validations
      if ($f === 'password') {
        if ($raw === '') continue; // skip empty password
        $val = password_hash($raw, PASSWORD_DEFAULT);
      } elseif ($f === 'email') {
        $val = filter_var($raw, FILTER_VALIDATE_EMAIL);
        if ($val === false) { $errors[] = 'Email inválido'; continue; }
      } elseif (in_array($f, ['two_factor_enabled','is_admin'])) {
        $val = ($raw === '1' || $raw === 1 || $raw === 'on') ? 1 : 0;
      } elseif ($f === 'reset_token_expires') {
        $val = intval($raw);
      } else {
        $val = $raw;
      }
      $fields[] = "`$f` = ?"; $params[] = $val;
    }
  }

  if (!empty($errors)) {
    // simple error output; admin UI can be extended to show nicer messages
    foreach ($errors as $e) echo '<div style="color:red">' . htmlspecialchars($e) . '</div>';
    exit;
  }

  if ($id && count($fields)) {
    $params[] = $id;
    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute($params);
    if ($ok) {
      // record which fields changed
      audit_log('user.update', $_SESSION['user']['id'] ?? null, ['id'=>$id,'fields'=>array_map(function($s){ return preg_replace('/`/','',$s); }, $fields)]);
    }
    header('Location: users.php'); exit;
  }
}

$stmt = $pdo->query('SELECT * FROM users ORDER BY id ASC');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Define remote-known fields (from remote schema)
$remoteKnown = ['email_verified_at','remember_token','totp_secret','two_factor_enabled','last_login_at','is_admin','reset_token','reset_token_expires'];

// Build display columns (stable order)
$cols = ['id','name','email','role','created_at','updated_at'];
foreach ($users as $u) { foreach ($u as $k=>$v) if (!in_array($k,$cols)) $cols[] = $k; }

// split into local vs remote
$localCols = [];
$remoteCols = [];
foreach ($cols as $c) {
    if (in_array($c, $remoteKnown)) $remoteCols[] = $c; else $localCols[] = $c;
}

// current user info for role checks
$current = current_user();

?><!doctype html>
<html><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CRM - Usuarios</title>
  <link rel="stylesheet" href="/xlerion.css">
  <style>
    .users-grid { width:100%; border-collapse:collapse }
    .users-grid th, .users-grid td { border:1px solid #ddd; padding:8px }
    .panel { padding:12px; border-radius:6px; background:#f8f9fa; margin-bottom:12px }
    .remote-panel { background:#fff8e6; border:1px solid #ffdca8 }
    .local-panel { background:#eef9ff; border:1px solid #cfeefd }
    .muted { color:#666; font-size:0.9em }
  </style>
</head><body>
<?php require_once __DIR__ . '/../../../includes/header.php'; ?>
<main style="padding:16px;">
  <h1>Usuarios</h1>
  <p class="muted">Edición separada: <strong>Campos locales</strong> y <strong>Campos remotos</strong>. Solo administradores pueden cambiar roles o `is_admin`.</p>

  <table class="users-grid"><tr>
    <?php foreach ($localCols as $c): ?><th><?=htmlspecialchars($c)?></th><?php endforeach; ?><th>Acciones</th></tr>

  <?php foreach ($users as $u): ?>
    <tr>
      <?php foreach ($localCols as $c): ?><td><?=htmlspecialchars($u[$c] ?? '')?></td><?php endforeach; ?>
      <td><a href="#" onclick="document.getElementById('edit-<?=$u['id']?>').style.display='block';return false;">Editar</a></td>
    </tr>
    <tr id="edit-<?=$u['id']?>" style="display:none"><td colspan="<?=count($localCols)+1?>">
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <div class="panel local-panel" style="flex:1;min-width:280px">
          <h3>Campos locales</h3>
          <form method="post" id="form-<?=$u['id']?>">
            <?=csrf_input_field()?>
            <input type="hidden" name="id" value="<?=$u['id']?>">
            <?php foreach ($localCols as $c): ?>
              <div style="margin-bottom:8px"><label><?=htmlspecialchars($c)?>:<br>
                <?php if ($c === 'password'): ?>
                  <input name="password" type="password" placeholder="(dejar vacío para mantener)">
                <?php else: ?>
                  <input name="<?=htmlspecialchars($c)?>" value="<?=htmlspecialchars($u[$c] ?? '')?>">
                <?php endif; ?>
              </label></div>
            <?php endforeach; ?>
            <div><button type="submit">Guardar</button> <button type="button" onclick="this.closest('tr').style.display='none'">Cancelar</button></div>
          </form>
        </div>
        <div class="panel remote-panel" style="width:360px">
          <h3>Campos remotos</h3>
          <p class="muted">Estos campos provienen del esquema remoto y son editables aquí.</p>
          <form method="post" class="remote-form" data-id="<?=$u['id']?>">
            <?=csrf_input_field()?>
            <input type="hidden" name="id" value="<?=$u['id']?>">
            <?php foreach ($remoteCols as $c): ?>
              <div style="margin-bottom:8px"><label><?=htmlspecialchars($c)?>:<br>
                <?php if (in_array($c,['two_factor_enabled','is_admin'])): ?>
                  <select name="<?=htmlspecialchars($c)?>">
                    <option value="0" <?=((empty($u[$c]) && $u[$c] != '0') ? '' : ($u[$c] ? 'selected' : ''))?>>0</option>
                    <option value="1" <?=(!empty($u[$c]) && $u[$c] != '0' ? 'selected' : '')?>>1</option>
                  </select>
                <?php elseif (in_array($c,['reset_token_expires'])): ?>
                  <input name="<?=htmlspecialchars($c)?>" type="number" value="<?=htmlspecialchars($u[$c] ?? '')?>">
                <?php else: ?>
                  <input name="<?=htmlspecialchars($c)?>" value="<?=htmlspecialchars($u[$c] ?? '')?>">
                <?php endif; ?>
              </label></div>
            <?php endforeach; ?>
            <div><button type="submit">Guardar remotos</button></div>
          </form>
        </div>
      </div>
    </td></tr>
  <?php endforeach; ?>
  </table>

</main>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>

<script>
// Client-side validation: prevent non-admins from toggling admin flag
document.addEventListener('submit', function(ev){
  var form = ev.target;
  if (!form || !form.matches) return;
  if (form.matches('.remote-form')) {
    var isAdminField = form.querySelector('[name="is_admin"]');
    if (isAdminField) {
      // server enforces role; here we warn non-admins
      var currentRole = '<?= htmlspecialchars($current['role'] ?? '') ?>';
      if (currentRole !== 'admin' && isAdminField.value === '1') {
        ev.preventDefault(); alert('Solo administradores pueden asignar privilegios de administrador.');
        return false;
      }
    }
  }
});
</script>
</body></html>
