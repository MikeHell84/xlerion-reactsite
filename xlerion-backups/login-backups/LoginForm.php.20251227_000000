<?php
function renderLoginForm($error = '') {
    ?>
    <div class="login-wrapper">
      <div class="row w-100 justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 d-flex justify-content-center">
          <div class="login-card text-center w-full max-w-sm">
            <img src="/media/LogoX.svg" alt="Logo Xlerion" class="mb-3" style="max-width:90px;max-height:90px;display:block;margin:0 auto;">
            <h1>Panel De Control</h1>
            <?php if (!empty($error)): ?><div class="error mb-2"><?=htmlspecialchars($error)?></div><?php endif; ?>
            <form method="post" autocomplete="off" id="login-form" class="w-full">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required autofocus autocomplete="username" class="form-control mb-2">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" class="form-control mb-3">
                <div class="btn-group">
                  <button type="submit" class="xlerion-btn-primary">Entrar</button>
                  <button type="button" class="xlerion-btn-secondary" id="forgot-password-btn">Olvidé mi contraseña</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php
}