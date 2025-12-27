<?php
// Global header include: loads Bootstrap 5 and Tailwind via CDN
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xlerion</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind (via CDN for utility classes) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/public/xlerion.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">Xlerion</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/inicio.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="/filosofia.php">Filosofía</a></li>
        <li class="nav-item"><a class="nav-link" href="/proyectos.php">Proyectos</a></li>
        <li class="nav-item"><a class="nav-link" href="/contacto.php">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>
