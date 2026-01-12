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
        // Dev-only: allow Simple Browser to view dashboard without manual login
        // This is strictly allowed only when APP_ENV=development.
        global $env;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (!empty($env['APP_ENV']) && strtolower($env['APP_ENV']) === 'development') {
            if (preg_match('/Simple Browser|SimpleBrowser|vscode|VS Code/i', $ua)) {
                    $_SESSION['user'] = ['id' => 1, 'username' => 'dev', 'role' => 'admin'];
                    return;
                }
                // Also allow localhost loopback requests in development (Simple Browser uses local loopback)
                $remote = $_SERVER['REMOTE_ADDR'] ?? '';
                if ($remote === '127.0.0.1' || $remote === '::1' || $remote === '::ffff:127.0.0.1') {
                    $_SESSION['user'] = ['id' => 1, 'username' => 'dev', 'role' => 'admin'];
                    return;
                }
        }
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

// CSRF helpers
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    if (empty($token)) return false;
    if (empty($_SESSION['csrf_token'])) return false;
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    // Optional expiry (2 hours)
    if ($valid && !empty($_SESSION['csrf_token_time'])) {
        if (time() - $_SESSION['csrf_token_time'] > 7200) return false;
    }
    return $valid;
}

function csrf_input_field() {
    $t = htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $t . '">';
}

// Simple audit logger (appends JSON lines)
function audit_log($action, $userId = null, $meta = []) {
    $dir = __DIR__ . '/../xlerion-backups';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $entry = [
        'ts' => date('c'),
        'action' => $action,
        'user_id' => $userId ?? ($_SESSION['user']['id'] ?? null),
        'meta' => $meta,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ];
    file_put_contents($dir . '/audit.log', json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
}
