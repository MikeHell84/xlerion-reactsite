<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /public/admin/migrations_review.php'); exit; }
$csrf = $_POST['csrf_token'] ?? null; if (!validate_csrf_token($csrf)) { http_response_code(403); echo 'invalid csrf'; exit; }
$sql = $_POST['sql'] ?? ''; if (empty($sql)) { echo 'No SQL provided'; exit; }
$fn = 'add_missing_columns_' . date('Ymd_His') . '.sql';
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $fn . '"');
echo $sql;
exit;
