<?php
require_once __DIR__ . '/../../../includes/config.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
if (!validate_csrf_token($token)) {
    http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']); exit;
}

$remoteDataPath = __DIR__ . '/../../../data/remote_data/cms_pages.json';
if (!file_exists($remoteDataPath)) {
    echo json_encode(['ok'=>false,'error'=>'Remote data file not found']); exit;
}

$json = json_decode(file_get_contents($remoteDataPath), true);
if (!is_array($json)) { echo json_encode(['ok'=>false,'error'=>'Invalid JSON']); exit; }

$pdo = null;
global $env;
if (!empty($env['LOCAL_DB_DATABASE'])) {
    $h = $env['LOCAL_DB_HOST'] ?? '127.0.0.1';
    $p = $env['LOCAL_DB_PORT'] ?? 3306;
    $dbn = $env['LOCAL_DB_DATABASE'];
    $u = $env['LOCAL_DB_USERNAME'] ?? ($env['DB_USERNAME'] ?? 'root');
    $pw = $env['LOCAL_DB_PASSWORD'] ?? ($env['DB_PASSWORD'] ?? '');
    try {
        $dsn = "mysql:host={$h};port={$p};dbname={$dbn};charset=utf8mb4";
        $pdo = new PDO($dsn, $u, $pw, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    } catch (Exception $e) { $pdo = null; }
}
if (!$pdo) { $pdo = try_get_pdo(); }
if (!$pdo) { echo json_encode(['ok'=>false,'error'=>'DB connection failed']); exit; }

$backupDir = __DIR__ . '/../../../xlerion-backups'; if (!is_dir($backupDir)) mkdir($backupDir,0755,true);
$now = date('Ymd_His'); $backupFile = $backupDir . '/pages_backup_' . $now . '.json';
try { $stmt = $pdo->query('SELECT * FROM `pages`'); $all = $stmt->fetchAll(PDO::FETCH_ASSOC); file_put_contents($backupFile, json_encode($all, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)); } catch (Exception $e) {}

$inserted=0; $updated=0; $skipped=0;
$pdo->beginTransaction();
try {
    $select = $pdo->prepare('SELECT id, slug, title, content FROM `pages` WHERE slug = :slug LIMIT 1');
    $ins = $pdo->prepare('INSERT INTO `pages` (slug, title, content, created_at, updated_at) VALUES (:slug, :title, :content, :created_at, :updated_at)');
    $upd = $pdo->prepare('UPDATE `pages` SET title = :title, content = :content, updated_at = :updated_at WHERE id = :id');
    foreach ($json as $row) {
        $slug = $row['slug'] ?? null; if (!$slug) { $skipped++; continue; }
        $select->execute([':slug'=>$slug]); $existing = $select->fetch(PDO::FETCH_ASSOC);
        $title = $row['title'] ?? $slug; $content = $row['content'] ?? '';
        $created_at = $row['created_at'] ?? date('Y-m-d H:i:s'); $updated_at = $row['updated_at'] ?? $created_at;
        if ($existing) {
            if ($existing['title'] !== $title || $existing['content'] !== $content) { $upd->execute([':title'=>$title,':content'=>$content,':updated_at'=>$updated_at,':id'=>$existing['id']]); $updated++; } else { $skipped++; }
        } else { $ins->execute([':slug'=>$slug,':title'=>$title,':content'=>$content,':created_at'=>$created_at,':updated_at'=>$updated_at]); $inserted++; }
    }
    $pdo->commit();
} catch (Exception $e) { $pdo->rollBack(); echo json_encode(['ok'=>false,'error'=>'Sync failed: '. $e->getMessage()]); exit; }

audit_log('sync_cms_pages_web', $_SESSION['user']['id'] ?? null, ['inserted'=>$inserted,'updated'=>$updated,'skipped'=>$skipped,'backup'=>$backupFile]);
echo json_encode(['ok'=>true,'inserted'=>$inserted,'updated'=>$updated,'skipped'=>$skipped,'backup'=>$backupFile]);
