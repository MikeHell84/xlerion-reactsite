<?php
header('Content-Type: application/json');
$resp = [
  'ok' => true,
  'message' => 'Auth stub. Implement login/register/2FA endpoints.',
  'routes' => [
    'login' => '/api/crm/auth.php?action=login',
    'register' => '/api/crm/auth.php?action=register',
    'logout' => '/api/crm/auth.php?action=logout'
  ]
];
echo json_encode($resp);
