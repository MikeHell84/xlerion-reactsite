<?php
class ModulesModel {
    public static function forPage($pageId) { require_once __DIR__ . '/../config.php'; 
        $pdo = try_get_pdo(); if (!$pdo) return []; 
        $stmt = $pdo->prepare('SELECT * FROM modules WHERE page_id = ? ORDER BY `order` ASC'); 
        $stmt->execute([$pageId]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}