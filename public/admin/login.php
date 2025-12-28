<?php
require_once __DIR__ . '/../../includes/config.php';
// Si ya está logueado, redirigir al dashboard
if (!empty($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Xlerion</title>
    <link href="/xlerion.css" rel="stylesheet">
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


