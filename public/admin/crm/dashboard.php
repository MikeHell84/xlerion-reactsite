<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
$user = current_user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>CRM Dashboard</title>
  <link href="/xlerion.css" rel="stylesheet">
  <style>body{padding:18px}</style>
</head>
<body>
  <h1>CRM — Dashboard</h1>
  <p>Usuario: <?=htmlspecialchars($user['username'])?> — <?=htmlspecialchars($user['role'])?></p>
  <p>Resumen rápido: Widgets e indicadores estarán aquí (prototipo).</p>
  <ul>
    <li><a href="/public/admin/crm/clients.php">Clientes</a></li>
    <li><a href="/public/admin/crm/leads.php">Leads</a></li>
    <li><a href="/public/admin/crm/opportunities.php">Oportunidades</a></li>
  </ul>
  <p><a href="/public/admin/index.php">Volver al panel</a></p>
</body>
</html>
