<?php
// Simple migration runner for development
$sqlFile = __DIR__ . '/migrations/001_init.sql';
if (!file_exists($sqlFile)) {
    echo "Migration file not found: $sqlFile\n";
    exit(1);
}

// Load .env-like file from project root
function load_env($path) {
    $env = [];
    if (!file_exists($path)) return $env;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
    return $env;
}

$env = load_env(__DIR__ . '/../.env');
$host = $env['DB_HOST'] ?? ($env['DB_HOSTNAME'] ?? '127.0.0.1');
$db   = $env['DB_NAME'] ?? ($env['DB_DATABASE'] ?? 'xlerion');
$user = $env['DB_USER'] ?? ($env['DB_USERNAME'] ?? 'root');
$pass = $env['DB_PASS'] ?? ($env['DB_PASSWORD'] ?? '');
$port = $env['DB_PORT'] ?? 3306;

$dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    // Create DB if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `".addslashes($db)."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `".addslashes($db)."`;");
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo "Migration applied successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
