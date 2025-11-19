<?php
require_once APP_ROOT . '/app/core/Database.php';

class User {
    public static function findByEmail(string $email): ?array {
        $pdo = Database::getInstance();
        // CHANGED: users -> doctors, select only what we need
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM doctors WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // You said signup is not used anymore, but let's keep it consistent
    public static function create(string $email, string $password, string $name = null): int {
        $pdo = Database::getInstance();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO doctors (name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$name, $email, $hash]);
        return (int)$pdo->lastInsertId();
    }
}
