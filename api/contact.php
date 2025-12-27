<?php
/**
 * Contact Form API Endpoint
 * POST /api/contact.php - Submit contact form
 */

require_once __DIR__ . '/base.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }

    $data = getRequestBody();
    
    // Validate required fields
    validateRequired($data, ['name', 'email', 'message']);

    // Sanitize input
    $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
    $message = filter_var($data['message'], FILTER_SANITIZE_STRING);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email address', 400);
    }

    // Get database connection
    $db = Database::getInstance()->getConnection();

    // Insert contact message (modify table name as needed)
    // $stmt = $db->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
    // $stmt->execute([$name, $email, $message]);

    sendResponse([
        'success' => true,
        'message' => 'Contact form submitted successfully'
    ]);

} catch (Exception $e) {
    error_log('Contact API Error: ' . $e->getMessage());
    sendError('Failed to submit contact form', 500);
}
