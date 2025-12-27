<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-5">
  <h1>Contacto</h1>
  <p class="lead">Escríbenos para consultas, propuestas o soporte.</p>

  <div class="row">
    <div class="col-12 col-md-6">
      <form method="post" action="/public/api/contact.php">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Correo</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mensaje</label>
          <textarea name="message" class="form-control" rows="6" required></textarea>
        </div>
        <button class="btn btn-primary">Enviar</button>
      </form>
    </div>
    <div class="col-12 col-md-6">
      <h5>Oficinas</h5>
      <address>Madrid, España<br/>Lun-Vie 09:00–18:00</address>
      <h5>Mapa</h5>
      <div style="height:240px;background:#f1f1f1;border-radius:8px;">Mapa interactivo (placeholder)</div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="container">
  <h1>Contacto</h1>
  <p>¿Deseas colaborar, invertir o conocer más sobre Xlerion? Estamos abiertos al diálogo.</p>
  <form method="post" action="/public/api/contact.php">
    <div class="mb-3"><label>Nombre</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label>Correo</label><input name="email" type="email" class="form-control" required></div>
    <div class="mb-3"><label>Mensaje</label><textarea name="message" class="form-control" rows="4" required></textarea></div>
    <button class="cta-btn" type="submit">Enviar</button>
  </form>
  <h3>Contactos</h3>
  <ul>
    <li>contactus@xlerion.com</li>
    <li>support@xlerion.com</li>
  </ul>
  <p><a href="https://wa.me/573208605600" class="cta-btn" style="background:#25D366">WhatsApp</a></p>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
