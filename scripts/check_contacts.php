<?php
require __DIR__ . '/../includes/config.php';
try{
    $pdo = get_pdo();
    $stmt = $pdo->query('SELECT COUNT(*) AS c FROM contacts');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "contacts_count:" . ($row['c'] ?? '0') . PHP_EOL;
}catch(Exception $e){
    echo "error:" . $e->getMessage() . PHP_EOL;
}
