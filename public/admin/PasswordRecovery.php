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

        // Build reset link
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host) {
          $resetLink = sprintf('%s://%s/admin/PasswordReset.php?token=%s', $scheme, $host, $token);
        } else {
          $resetLink = '/admin/PasswordReset.php?token=' . $token;
        }

        // Try to send the reset link by email. Use .env SMTP settings if available.
        $sent = false;
        global $env;
        $from = $env['MAIL_FROM'] ?? ('no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $subject = 'Recuperación de contraseña - Xlerion';
        $body = "Hola,\n\nSe ha solicitado restablecer la contraseña para esta cuenta.\n\nUsa el siguiente enlace para restablecerla:\n\n" . $resetLink . "\n\nSi no solicitaste esto, ignora este mensaje.\n";

        // If SMTP host is configured, try basic SMTP send; otherwise try mail().
        if (!empty($env['SMTP_HOST'])) {
          $smtpHost = $env['SMTP_HOST'];
          $smtpPort = $env['SMTP_PORT'] ?? 25;
          $smtpUser = $env['SMTP_USER'] ?? '';
          $smtpPass = $env['SMTP_PASS'] ?? '';

          // Basic SMTP client (may fail on servers requiring TLS). Return true on success.
          $sent = false;
          $fp = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
          if ($fp) {
            $res = fgets($fp, 512);
            fputs($fp, "EHLO localhost\r\n");
            $res = fgets($fp, 512);
            if (!empty($smtpUser) && !empty($smtpPass)) {
              fputs($fp, "AUTH LOGIN\r\n");
              fgets($fp,512);
              fputs($fp, base64_encode($smtpUser) . "\r\n");
              fgets($fp,512);
              fputs($fp, base64_encode($smtpPass) . "\r\n");
              fgets($fp,512);
            }
            fputs($fp, "MAIL FROM: <{$from}>\r\n");
            fgets($fp,512);
            fputs($fp, "RCPT TO: <{$email}>\r\n");
            fgets($fp,512);
            fputs($fp, "DATA\r\n");
            fgets($fp,512);
            $headers = "From: {$from}\r\n" .
                   "Reply-To: {$from}\r\n" .
                   "MIME-Version: 1.0\r\n" .
                   "Content-Type: text/plain; charset=UTF-8\r\n" .
                   "Subject: {$subject}\r\n" .
                   "\r\n";
            fputs($fp, $headers . $body . "\r\n.\r\n");
            $res = fgets($fp,512);
            fputs($fp, "QUIT\r\n");
            fclose($fp);
            // crude success check
            if (strpos($res, '250') !== false || strpos($res, '354') !== false) $sent = true;
          }
        } else {
          // Try PHP mail() as a fallback
          $headers = 'From: ' . $from . "\r\n" . 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
          $sent = @mail($email, $subject, $body, $headers);
        }

        if ($sent) {
          $message = 'Se ha enviado un correo con instrucciones a ' . htmlspecialchars($email) . '.';
        } else {
          $message = 'No se ha podido enviar el correo. Enlace de recuperación (solo dev):';
        }
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