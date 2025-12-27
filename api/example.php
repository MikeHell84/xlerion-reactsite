<?php
/**
 * Example API Endpoint
 * GET /api/example.php - Get example data
 */

require_once __DIR__ . '/base.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        sendError('Method not allowed', 405);
    }

    // Get database connection
    $db = Database::getInstance()->getConnection();

    // Example query (modify based on your database schema)
    // $stmt = $db->prepare("SELECT * FROM your_table LIMIT 10");
    // $stmt->execute();
    // $results = $stmt->fetchAll();

    // For now, return example data
    $results = [
        [
            'id' => 1,
            'title' => 'Welcome to Xlerion',
            'description' => 'This is an example API endpoint'
        ],
        [
            'id' => 2,
            'title' => 'Built with PHP 8',
            'description' => 'Using MariaDB and modern stack'
        ]
    ];

    sendResponse([
        'success' => true,
        'data' => $results
    ]);

} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    sendError('Internal server error', 500);
}
