<?php
/**
 * Simple PHP Development Server Router
 * This file routes requests to the appropriate location
 */

$request_uri = $_SERVER['REQUEST_URI'];
$request_path = parse_url($request_uri, PHP_URL_PATH);

// API routes
if (strpos($request_path, '/api/') === 0) {
    // Let PHP handle API requests
    return false;
}

// Serve static files
$file = __DIR__ . '/public' . $request_path;
if (is_file($file)) {
    return false;
}

// Fallback to index.html for SPA routing
if (file_exists(__DIR__ . '/public/index.html')) {
    include __DIR__ . '/public/index.html';
} else {
    http_response_code(404);
    echo "404 - Page not found. Please build the project first with 'npm run build'.";
}
