<?php
require_once __DIR__ . '/../../includes/config.php';
// Si ya está logueado, redirigir al dashboard
if (!empty($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
    header('Location: index.php');
    exit;
}
// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $logged = false;
    // Try DB auth if available
    $pdo = try_get_pdo();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                $_SESSION['user'] = $user;
                header('Location: index.php');
                exit;
            }
        } catch (Exception $e) {
            // fallthrough to env-based check
        }
    }

    // Fallback: check ADMIN_PASS_HASH in .env for a single admin account
    $env = $GLOBALS['env'] ?? [];
    $hash = $env['ADMIN_PASS_HASH'] ?? '';
    if ($username === ($env['ADMIN_USER'] ?? 'admin') && $hash && password_verify($password, $hash)) {
        $_SESSION['user'] = ['id' => 1, 'username' => $username, 'role' => 'admin'];
        header('Location: index.php');
        exit;
    }

    $error = 'Credenciales inválidas';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Xlerion</title>
    <link href="/xlerion.css?v=<?php echo filemtime(__DIR__ . '/../xlerion.css'); ?>" rel="stylesheet">
</head>
<body class="admin-login-page">
    <div class="parallax-bg" aria-hidden="true"></div>
    <div class="admin-login-wrapper">
    <div class="login-box">
        <img src="/media/LogoX.svg" alt="Xlerion" class="login-logo" />
        <div class="login-title">Panel Admin</div>
        <?php if (isset($error)) { echo '<div class="alert alert-danger">'.$error.'</div>'; } ?>
        <form method="post" action="login.php" autocomplete="off">
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus autocomplete="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="xlerion-btn-primary">Ingresar</button>
            <a href="PasswordRecovery.php" class="forgot-password-link">¿Olvidaste tu contraseña?</a>
        </form>
    </div>
    </div>
</body>
    <script src="/admin/login-parallax.js"></script>
</body>
</html>


