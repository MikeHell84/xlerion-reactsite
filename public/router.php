<?php
// Router for PHP built-in server
// Enable verbose errors in development when using built-in server
@ini_set('display_errors', '1');
@ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// Serve static files in /public, also allow PHP pages located in project root.
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicDir = __DIR__;
$projectRoot = dirname(__DIR__);

// Normalize path to avoid traversal
if (strpos($uri, '..') !== false) {
    http_response_code(400);
    echo "400 Bad Request";
    exit;
}

$publicPath = $publicDir . $uri;
$projectPath = $projectRoot . $uri;

// If the file exists directly in public (static or php)
if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
    if (strtolower($ext) === 'php') {
        require $publicPath;
        exit;
    }
    // Let the built-in server serve static file
    return false;
}

// If the file exists in project root (e.g., /inicio.php)
if ($uri !== '/' && file_exists($projectPath) && !is_dir($projectPath)) {
    $ext = pathinfo($projectPath, PATHINFO_EXTENSION);
    if (strtolower($ext) === 'php') {
        require $projectPath;
        exit;
    }
    return false;
}

// Fallback: serve public/index.php if present
$index = $publicDir . '/index.php';
if (file_exists($index)) {
    require $index;
    exit;
}

http_response_code(404);
echo "404 Not Found - " . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
