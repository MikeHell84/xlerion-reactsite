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
