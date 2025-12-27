<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/LoginForm.php';
require_once __DIR__ . '/PasswordRecovery.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            header('Location: /public/admin/index.php');
            exit;
        }
        $error = 'Credenciales inválidas';
    } catch (Exception $e) {
        $error = 'Error de servidor';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Login</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link href="/admin/daisy-tailwind.css" rel="stylesheet">
  <link href="/admin/admin-login.css" rel="stylesheet">
  <!-- Compiled login styles (Login.module.scss -> login.css) -->
  <link href="/admin/login.css" rel="stylesheet">
</head>
<body>
  <main>
    <div class="parallax-bg" id="parallax-bg"></div>
    <?php renderLoginForm($error ?? ''); ?>
    <?php renderPasswordRecoveryForm(); ?>
  </main>
  <script>
    // Parallax fondo con imagen
    const bg = document.getElementById('parallax-bg');
    document.addEventListener('mousemove', function(e) {
      const x = (e.clientX / window.innerWidth - 0.5) * 30;
      const y = (e.clientY / window.innerHeight - 0.5) * 30;
      bg.style.transform = `translate(${x}px, ${y}px) scale(1.05)`;
    });
    window.addEventListener('resize', function() {
      bg.style.width = window.innerWidth + 'px';
      bg.style.height = window.innerHeight + 'px';
    });
    bg.style.width = window.innerWidth + 'px';
    bg.style.height = window.innerHeight + 'px';

    // Lógica para mostrar/ocultar recuperación de contraseña
    const forgotBtn = document.getElementById('forgot-password-btn');
    const loginForm = document.getElementById('login-form');
    const recoveryForm = document.getElementById('recovery-form');
    const cancelRecovery = document.getElementById('cancel-recovery');
    if (forgotBtn && loginForm && recoveryForm && cancelRecovery) {
      forgotBtn.addEventListener('click', function() {
        loginForm.style.display = 'none';
        recoveryForm.style.display = 'block';
      });
      cancelRecovery.addEventListener('click', function() {
        recoveryForm.style.display = 'none';
        loginForm.style.display = 'block';
      });
    }
  </script>
</body>
</html>
