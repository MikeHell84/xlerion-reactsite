<?php
require __DIR__ . '/../includes/config.php';
try{
    $pdo = get_pdo();
    $stmt = $pdo->query("SHOW CREATE TABLE contacts");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo ($row['Create Table'] ?? json_encode($row)) . PHP_EOL;
}catch(Exception $e){
    echo "error: " . $e->getMessage() . PHP_EOL;
}
