<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="container py-5">
  <h1>Blog</h1>
  <p class="lead">Artículos, reflexiones y noticias.</p>

  <section class="row">
    <div class="col-12 col-md-4 mb-3">
      <div class="card xlerion-card">
        <img src="https://via.placeholder.com/400x220.png?text=Artículo+1" class="card-img-top img-fluid" alt="Artículo 1">
        <div class="card-body"><h5>Artículo 1</h5><p class="small">Resumen corto del artículo.</p><a href="#" class="link-primary">Leer</a></div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
      <div class="card xlerion-card">
        <img src="https://via.placeholder.com/400x220.png?text=Artículo+2" class="card-img-top img-fluid" alt="Artículo 2">
        <div class="card-body"><h5>Artículo 2</h5><p class="small">Resumen corto del artículo.</p><a href="#" class="link-primary">Leer</a></div>
      </div>
    </div>
    <div class="col-12 col-md-4 mb-3">
      <div class="card xlerion-card">
        <img src="https://via.placeholder.com/400x220.png?text=Artículo+3" class="card-img-top img-fluid" alt="Artículo 3">
        <div class="card-body"><h5>Artículo 3</h5><p class="small">Resumen corto del artículo.</p><a href="#" class="link-primary">Leer</a></div>
      </div>
    </div>
  </section>

  <section class="mt-4">
    <h4>Newsletter</h4>
    <form class="d-flex gap-2" action="/public/api/contact.php" method="post">
      <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required>
      <button class="btn btn-primary">Suscribir</button>
    </form>
  </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
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
