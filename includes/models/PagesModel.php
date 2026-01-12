<?php
class PagesModel {
    public static function all() {
        require_once __DIR__ . '/../config.php';
        $pdo = try_get_pdo(); if (!$pdo) return [];
        $stmt = $pdo->query('SELECT * FROM pages ORDER BY id ASC'); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}