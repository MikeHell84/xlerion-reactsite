<?php
/**
 * API Base Configuration
 * Common functions and headers for all API endpoints
 */

// Set headers for JSON API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

/**
 * Send JSON response
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Send error response
 */
function sendError($message, $statusCode = 400) {
    sendResponse([
        'error' => true,
        'message' => $message
    ], $statusCode);
}

/**
 * Get request body as JSON
 */
function getRequestBody() {
    $json = file_get_contents('php://input');
    return json_decode($json, true);
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            sendError("Missing required field: $field", 400);
        }
    }
}
