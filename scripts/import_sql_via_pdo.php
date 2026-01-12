<?php
// Import an SQL file into the local DB using PDO (safer across shells)
require_once __DIR__ . '/../includes/config.php';

$env = load_env(__DIR__ . '/../.env');
$host = $env['LOCAL_DB_HOST'] ?? '127.0.0.1';
$port = $env['LOCAL_DB_PORT'] ?? 3306;
$db = $env['LOCAL_DB_DATABASE'] ?? 'xlerionc_xlerion_db_test';
$user = $env['LOCAL_DB_USERNAME'] ?? 'root';
$pass = $env['LOCAL_DB_PASSWORD'] ?? '';

$sqlFile = __DIR__ . '/../remote_schema_only_from_json.sql';
if (!file_exists($sqlFile)) {
    fwrite(STDERR, "SQL file not found: $sqlFile\n");
    exit(1);
}

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ]);
} catch (Exception $e) {
    fwrite(STDERR, "Could not connect to local DB: " . $e->getMessage() . "\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
// Split statements by ; followed by newline (basic)
$parts = preg_split('/;\s*\n/', $sql);
$count = 0;
foreach ($parts as $part) {
    $s = trim($part);
    if ($s === '' ) continue;
    try {
        $pdo->exec($s);
        $count++;
    } catch (Exception $e) {
        fwrite(STDERR, "Error executing statement: " . $e->getMessage() . "\nStatement: " . substr($s,0,200) . "...\n");
        // continue attempting remaining statements
    }
}

fwrite(STDOUT, "Executed ~{$count} statements into {$db}\n");
return 0;
