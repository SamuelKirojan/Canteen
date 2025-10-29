<?php
require_once APP_ROOT . '/app/core/Database.php';

class Admin {
    public static function findByEmail(string $email): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $email, string $password): int {
        $pdo = Database::getInstance();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO admins (email, password_hash, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$email, $hash]);
        return (int)$pdo->lastInsertId();
    }
}
