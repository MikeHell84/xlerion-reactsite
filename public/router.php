<?php
// Simple router for PHP built-in server
// If the requested file exists, return false so the server serves it directly.
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
// Map requests to project root so URIs that include /public/ still resolve correctly
$projectRoot = dirname(__DIR__);
$file = $projectRoot . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Avoid running arbitrary files outside public; map unknown requests to a simple index or 404.
$index = __DIR__ . '/index.php';
if (file_exists($index)) {
    require $index;
    exit;
}

http_response_code(404);
echo "404 Not Found - " . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
