<?php
require_once __DIR__ . '/../../includes/config.php';

// Simple password recovery flow for development:
// - Accept an email, create a one-time token and store it in data/password_resets.json
// - Show a reset link (for dev) so the admin can complete the reset

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

$message = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['recovery_email'] ?? '');
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Introduce un correo válido.';
    } else {
        $token = bin2hex(random_bytes(16));
        $expires = time() + 3600; // 1 hour
        $resets = load_resets($resetsFile);
        $resets[] = [
            'email' => $email,
            'token' => $token,
            'expires' => $expires,
            'used' => false,
            'created_at' => time(),
        ];
        save_resets($resetsFile, $resets);

        // For development we show the reset link so it's easy to test.
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host) {
            $resetLink = sprintf('%s://%s/admin/PasswordReset.php?token=%s', $scheme, $host, $token);
        } else {
            $resetLink = '/admin/PasswordReset.php?token=' . $token;
        }
        $message = 'Se ha generado un enlace de recuperación (visible sólo en entorno de desarrollo).';
    }
}

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Recuperar contraseña - Admin</title>
  <link href="/xlerion.css?v=<?php echo filemtime(__DIR__ . '/../xlerion.css'); ?>" rel="stylesheet">
</head>
<body class="admin-login-page">
  <div class="parallax-bg" aria-hidden="true"></div>
  <div class="admin-login-wrapper">
    <div class="login-box">
      <img src="/media/LogoX.svg" alt="Xlerion" class="login-logo" />
      <h2 class="login-title">Recuperar acceso</h2>
      <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <?php if ($resetLink): ?>
        <p>Enlace de recuperación (solo para pruebas):</p>
        <p><a href="<?php echo htmlspecialchars($resetLink); ?>"><?php echo htmlspecialchars($resetLink); ?></a></p>
      <?php endif; ?>

      <form method="post" autocomplete="off" id="recovery-form" class="recovery-form">
        <label for="recovery-email" class="form-label">Introduce tu correo para recuperar el acceso</label>
        <input type="email" id="recovery-email" name="recovery_email" required placeholder="Tu correo electrónico" class="form-control mb-3">
        <div style="display:flex;gap:8px;">
          <button type="submit" class="xlerion-btn-primary">Enviar enlace</button>
          <a class="xlerion-btn" href="login.php">Volver</a>
        </div>
      </form>
    </div>
  </div>
  <script src="/admin/login-parallax.js"></script>
</body>
</html>
<?php
function renderPasswordRecoveryForm() {
    ?>
    <form method="post" autocomplete="off" id="recovery-form" class="recovery-form" style="display:none">
        <label for="recovery-email">Recuperar acceso</label>
        <input type="email" id="recovery-email" name="recovery_email" required placeholder="Tu correo electrónico" class="form-control mb-3">
        <button type="submit" class="daisy-btn">Enviar enlace</button>
        <button type="button" class="daisy-btn-accent" id="cancel-recovery">Cancelar</button>
    </form>
    <?php
}