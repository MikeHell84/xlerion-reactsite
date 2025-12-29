<?php
require_once __DIR__ . '/../includes/config.php';

// CLI-friendly sync: reads data/remote_data/cms_pages.json and upserts into local `pages` table
// Backup existing pages to xlerion-backups/pages_backup_TIMESTAMP.json

$remoteDataPath = __DIR__ . '/../data/remote_data/cms_pages.json';
if (!file_exists($remoteDataPath)) {
    echo "Remote data file not found: $remoteDataPath\n";
    exit(1);
}

$json = json_decode(file_get_contents($remoteDataPath), true);
if (!is_array($json)) {
    echo "Invalid JSON in remote data file.\n";
    exit(1);
}

$pdo = try_get_pdo();
if (!$pdo) { echo "Cannot connect to local DB (get_pdo failed).\n"; exit(1); }
// prefer explicit LOCAL_DB_* settings if present in .env (useful for syncing into a local test DB)
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
if (!$pdo) { echo "Cannot connect to local DB (get_pdo failed).\n"; exit(1); }

// backup current pages
$backupDir = __DIR__ . '/../xlerion-backups'; if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
$now = date('Ymd_His');
$backupFile = $backupDir . '/pages_backup_' . $now . '.json';
try {
    $stmt = $pdo->query('SELECT * FROM `pages`');
    $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    file_put_contents($backupFile, json_encode($all, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    echo "Backup written to: $backupFile\n";
} catch (Exception $e) {
    echo "Warning: could not backup pages table: " . $e->getMessage() . "\n";
}

$inserted = 0; $updated = 0; $skipped = 0;
$pdo->beginTransaction();
try {
    $select = $pdo->prepare('SELECT id, slug, title, content, created_at, updated_at FROM `pages` WHERE slug = :slug LIMIT 1');
    $ins = $pdo->prepare('INSERT INTO `pages` (slug, title, content, created_at, updated_at) VALUES (:slug, :title, :content, :created_at, :updated_at)');
    $upd = $pdo->prepare('UPDATE `pages` SET title = :title, content = :content, updated_at = :updated_at WHERE id = :id');

    foreach ($json as $row) {
        $slug = $row['slug'] ?? null;
        if (!$slug) { $skipped++; continue; }
        $select->execute([':slug' => $slug]);
        $existing = $select->fetch(PDO::FETCH_ASSOC);
        $title = $row['title'] ?? ($row['slug'] ?? '');
        $content = $row['content'] ?? '';
        $created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
        $updated_at = $row['updated_at'] ?? $created_at;
        if ($existing) {
            // compare and update if different
            if ($existing['title'] !== $title || $existing['content'] !== $content) {
                $upd->execute([':title'=>$title, ':content'=>$content, ':updated_at'=>$updated_at, ':id'=>$existing['id']]);
                $updated++;
            } else {
                $skipped++;
            }
        } else {
            $ins->execute([':slug'=>$slug, ':title'=>$title, ':content'=>$content, ':created_at'=>$created_at, ':updated_at'=>$updated_at]);
            $inserted++;
        }
    }
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error during sync: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Sync complete. Inserted: $inserted, Updated: $updated, Skipped: $skipped\n";
audit_log('sync_cms_pages', null, ['inserted'=>$inserted,'updated'=>$updated,'skipped'=>$skipped,'backup'=>$backupFile]);
return 0;
