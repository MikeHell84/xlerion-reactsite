<?php
/**
 * Database Connection Handler
 * Connects to MariaDB using credentials from .env file
 */

require_once __DIR__ . '/env.php';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            // Load environment variables
            EnvLoader::load(__DIR__ . '/../.env');

            // Get database credentials from environment
            $host = EnvLoader::get('DB_HOST', 'localhost');
            $port = EnvLoader::get('DB_PORT', '3306');
            $dbname = EnvLoader::get('DB_NAME', 'xlerion_db');
            $username = EnvLoader::get('DB_USER', 'root');
            $password = EnvLoader::get('DB_PASS', '');
            $charset = EnvLoader::get('DB_CHARSET', 'utf8mb4');

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Singleton cannot be unserialized. Create a new instance using getInstance() instead.");
    }
}
