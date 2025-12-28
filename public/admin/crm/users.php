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
    $fields = [];
    $params = [];
    $allowed = ['name','email','password','totp_secret','two_factor_enabled','last_login_at','is_admin','reset_token','reset_token_expires'];
    foreach ($allowed as $f) {
        if (isset($_POST[$f])) {
            if ($f === 'password' && $_POST[$f] === '') continue; // skip empty password
            if ($f === 'password') { $val = password_hash($_POST[$f], PASSWORD_DEFAULT); }
            else { $val = $_POST[$f]; }
            $fields[] = "`$f` = ?"; $params[] = $val;
        }
    }
    if ($id && count($fields)) {
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute($params);
        audit_log('user.update', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
        header('Location: users.php'); exit;
    }
}

$stmt = $pdo->query('SELECT * FROM users ORDER BY id ASC');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
// determine columns to display (union of remote fields and local)
$cols = [];
foreach ($users as $u) { foreach ($u as $k=>$v) $cols[$k]=true; }
$cols = array_keys($cols);

?><!doctype html>
<html><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CRM - Usuarios</title>
  <link rel="stylesheet" href="/xlerion.css">
</head><body>
<?php require_once __DIR__ . '/../../../includes/header.php'; ?>
<main style="padding:16px;">
  <h1>Usuarios</h1>
  <p>Lista de usuarios. Puedes editar campos descubiertos en el esquema remoto (totp_secret, two_factor_enabled, last_login_at, is_admin, reset_token, reset_token_expires).</p>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%"><tr>
  <?php foreach ($cols as $c): ?><th><?=htmlspecialchars($c)?></th><?php endforeach; ?><th>Acciones</th></tr>
  <?php foreach ($users as $u): ?><tr>
    <?php foreach ($cols as $c): ?><td><?=htmlspecialchars($u[$c] ?? '')?></td><?php endforeach; ?>
    <td><a href="#" onclick="document.getElementById('edit-<?=$u['id']?>').style.display='block';return false;">Editar</a></td>
  </tr>
  <tr id="edit-<?=$u['id']?>" style="display:none"><td colspan="<?=count($cols)+1?>">
    <form method="post">
      <?=csrf_input_field()?>
      <input type="hidden" name="id" value="<?=$u['id']?>">
      <?php foreach ($cols as $c): ?>
        <div style="margin-bottom:6px"><label><?=htmlspecialchars($c)?>:<br>
        <?php if ($c === 'password'): ?>
          <input name="password" type="password" placeholder="(dejar vacío para mantener)">
        <?php elseif ($c === 'content' || $c === 'remember_token' || $c === 'reset_token' || $c === 'totp_secret'): ?>
          <input name="<?=htmlspecialchars($c)?>" value="<?=htmlspecialchars($u[$c] ?? '')?>">
        <?php else: ?>
          <input name="<?=htmlspecialchars($c)?>" value="<?=htmlspecialchars($u[$c] ?? '')?>">
        <?php endif; ?>
        </label></div>
      <?php endforeach; ?>
      <div><button type="submit">Guardar</button> <button type="button" onclick="this.closest('tr').style.display='none'">Cancelar</button></div>
    </form>
  </td></tr>
  <?php endforeach; ?></table>
</main>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
</body></html>
