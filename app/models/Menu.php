<?php
class Menu {
    public static function all(): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->query('SELECT id, name, price, stock, description, image_url FROM menu ORDER BY id ASC');
        return $stmt->fetchAll();
    }
}
