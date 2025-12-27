<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /public/admin/index.php');
    exit;
}

$slug = preg_replace('/[^a-z0-9\-\_]/i','', ($_POST['slug'] ?? ''));
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (!$slug || !$title) {
    echo "Slug y title son requeridos";
    exit;
}

try {
    $pdo = get_pdo();
    // backup current pages table to file
    $backupPath = __DIR__ . '/../../xlerion-backups/pages_backup_' . date('Ymd_His') . '.sql';
    // lightweight: insert backup record
    $stmtB = $pdo->prepare('INSERT INTO backups (name, path) VALUES (?, ?)');
    $stmtB->execute(["pages_backup_".date('Ymd_His'), $backupPath]);

    $stmt = $pdo->prepare('INSERT INTO pages (slug,title,content) VALUES (?,?,?)');
    $stmt->execute([$slug, $title, $content]);
    header('Location: /public/admin/index.php?page=list_pages');
    exit;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
