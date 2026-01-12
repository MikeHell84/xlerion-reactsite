<?php
// Fallback index for dev server
require_once __DIR__ . '/../includes/header.php';
?>
<main class="container mt-4">
  <h1>Bienvenido a Xlerion</h1>
  <p>Sitio en desarrollo — estado de la demo.</p>
  <ul>
    <li><a href="/public/admin/index.php">Panel Admin</a></li>
    <li><a href="/public/api/pages.php">API: pages</a></li>
    <li><a href="/frontend/">Frontend (fuente)</a></li>
  </ul>
</main>
<?php
require_once __DIR__ . '/../includes/footer.php';
