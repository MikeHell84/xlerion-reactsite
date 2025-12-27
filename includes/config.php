<?php
session_start();

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

function get_pdo() {
    global $env;
    $host = $env['DB_HOST'] ?? ($env['DB_HOSTNAME'] ?? '127.0.0.1');
    $db   = $env['DB_NAME'] ?? ($env['DB_DATABASE'] ?? 'xlerion');
    $user = $env['DB_USER'] ?? ($env['DB_USERNAME'] ?? 'root');
    $pass = $env['DB_PASS'] ?? ($env['DB_PASSWORD'] ?? '');
    $port = $env['DB_PORT'] ?? 3306;
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function try_get_pdo() {
    try {
        return get_pdo();
    } catch (Exception $e) {
        return null;
    }
}

function require_login() {
    if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        header('Location: /public/admin/login.php');
        exit;
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function backup_file($sourcePath, $destDir = null) {
    $destDir = $destDir ?? __DIR__ . '/../xlerion-backups';
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    $timestamp = date('Ymd_His');
    $base = basename($sourcePath);
    $dest = $destDir . '/' . $base . '.' . $timestamp;
    if (@copy($sourcePath, $dest)) {
        return $dest;
    }
    return false;
}
