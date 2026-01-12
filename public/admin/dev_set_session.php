<?php
require_once __DIR__ . '/../../includes/config.php';
// Only allow in development
if (empty($env['APP_ENV']) || strtolower($env['APP_ENV']) !== 'development') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}
// Create a dev session user
$_SESSION['user'] = ['id' => 1, 'username' => 'dev', 'role' => 'admin'];
echo session_id();
