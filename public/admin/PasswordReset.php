<?php
require_once __DIR__ . '/../../includes/config.php';

$dataDir = __DIR__ . '/../../data';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
$resetsFile = $dataDir . '/password_resets.json';

function load_resets($file) {
    if (!file_exists($file)) return [];
    $json = @file_get_contents($file);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

function save_resets($file, $arr) {
    return file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$message = '';

if (!$token) {
    $message = 'Token inválido.';
} else {
    $resets = load_resets($resetsFile);
    $matchKey = null;
    foreach ($resets as $k => $r) {
        if (!empty($r['token']) && hash_equals($r['token'], $token)) { $matchKey = $k; break; }
    }
    if ($matchKey === null) {
        $message = 'Token no encontrado o inválido.';
    } else {
        $record = $resets[$matchKey];
        if (!empty($record['used'])) {
            $message = 'Este enlace ya fue usado.';
        } elseif (time() > ($record['expires'] ?? 0)) {
            $message = 'El enlace ha expirado.';
        } else {
            // valid token
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $pw = $_POST['password'] ?? '';
                $pw2 = $_POST['password_confirm'] ?? '';
                if (strlen($pw) < 8) {
                    $message = 'La contraseña debe tener al menos 8 caracteres.';
                } elseif ($pw !== $pw2) {
                    $message = 'Las contraseñas no coinciden.';
                } else {
                    // update admin password hash in .env (development helper)
                    $envPath = __DIR__ . '/../../.env';
                    if (file_exists($envPath)) {
                        $envContents = file($envPath, FILE_IGNORE_NEW_LINES);
                        foreach ($envContents as &$line) {
                            if (strpos($line, 'ADMIN_PASS_HASH=') === 0) {
                                $line = 'ADMIN_PASS_HASH=' . password_hash($pw, PASSWORD_DEFAULT);
                                break;
                            }
                        }
                        file_put_contents($envPath, implode("\n", $envContents) . "\n");
                        // mark token used
                        $resets[$matchKey]['used'] = true;
                        save_resets($resetsFile, $resets);
                        $message = 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.';
                    } else {
                        $message = 'No se pudo actualizar la contraseña: archivo .env no encontrado.';
                    }
                }
            }
        }
    }
}

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Restablecer contraseña - Admin</title>
  <link href="/xlerion.css?v=<?php echo filemtime(__DIR__ . '/../xlerion.css'); ?>" rel="stylesheet">
</head>
<body class="admin-login-page">
  <div class="parallax-bg" aria-hidden="true"></div>
  <div class="admin-login-wrapper">
    <div class="login-box">
      <img src="/media/LogoX.svg" alt="Xlerion" class="login-logo" />
      <h2 class="login-title">Restablecer contraseña</h2>
      <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <?php if (empty($message) || strpos($message,'Contraseña actualizada') === false): ?>
        <form method="post" autocomplete="off">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
          <label for="password" class="form-label">Nueva contraseña</label>
          <input type="password" id="password" name="password" class="form-control mb-3" required>
          <label for="password_confirm" class="form-label">Confirmar contraseña</label>
          <input type="password" id="password_confirm" name="password_confirm" class="form-control mb-3" required>
          <div style="display:flex;gap:8px;">
            <button type="submit" class="xlerion-btn-primary">Actualizar contraseña</button>
            <a class="xlerion-btn" href="login.php">Volver</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <script src="/admin/login-parallax.js"></script>
</body>
</html>
