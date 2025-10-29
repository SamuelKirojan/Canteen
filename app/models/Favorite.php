<?php
require_once APP_ROOT . '/app/core/Database.php';

class Favorite {
    public static function add(int $userId, int $menuId): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('INSERT INTO favorites (user_id, menu_id, created_at) VALUES (?, ?, NOW())');
            return $stmt->execute([$userId, $menuId]);
        } catch (Exception $e) {
            error_log('Favorite add error: ' . $e->getMessage());
            return false;
        }
    }

    public static function remove(int $userId, int $menuId): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('DELETE FROM favorites WHERE user_id = ? AND menu_id = ?');
            return $stmt->execute([$userId, $menuId]);
        } catch (Exception $e) {
            error_log('Favorite remove error: ' . $e->getMessage());
            return false;
        }
    }

    public static function isFavorite(int $userId, int $menuId): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND menu_id = ?');
            $stmt->execute([$userId, $menuId]);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log('Favorite check error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getByUser(int $userId): array {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('
                SELECT m.*, f.created_at as favorited_at 
                FROM favorites f
                JOIN menu m ON f.menu_id = m.id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC
            ');
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Favorite list error: ' . $e->getMessage());
            return [];
        }
    }

    public static function getUserFavoriteIds(int $userId): array {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('SELECT menu_id FROM favorites WHERE user_id = ?');
            $stmt->execute([$userId]);
            return array_column($stmt->fetchAll(), 'menu_id');
        } catch (Exception $e) {
            error_log('Favorite IDs error: ' . $e->getMessage());
            return [];
        }
    }
}
