<?php require_once __DIR__ . '/includes/header.php'; ?>
<main class="container">
  <h1>Blog / Bitácora</h1>
  <p>Reflexiones, avances y documentación viva.</p>
  <ul>
    <li><a href="#">El origen de Total Darkness</a></li>
    <li><a href="#">Filosofía modular en videojuegos</a></li>
    <li><a href="#">Documentar para empoderar</a></li>
  </ul>
  <h3>Suscríbete al newsletter</h3>
  <form method="post" action="/public/api/newsletter.php">
    <input type="email" name="email" class="form-control" placeholder="Tu correo">
    <button class="cta-btn" type="submit">Suscribirse</button>
  </form>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
