<?php
require __DIR__ . '/../includes/config.php';

$pdo = try_get_pdo();
if (!$pdo) {
    echo "No DB connection via PDO\n";
    exit(1);
}

$stmt = $pdo->prepare('INSERT INTO crm_customers (name,email,status,created_at) VALUES (?,?,?,NOW())');
$stmt->execute(['CLI Test Customer','cli+20251228@example.com','active']);
$id = $pdo->lastInsertId();
echo "Inserted id: $id\n";

$stmt = $pdo->prepare('SELECT id,name,email,created_at FROM crm_customers WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
