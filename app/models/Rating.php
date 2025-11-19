<?php
class Rating {
    public static function add(int $orderId, int $userId, int $rating, string $review = ''): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('
                INSERT INTO order_ratings (order_id, user_id, rating, review, created_at) 
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE rating = ?, review = ?
            ');
            return $stmt->execute([$orderId, $userId, $rating, $review, $rating, $review]);
        } catch (Exception $e) {
            error_log('Rating add error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getByOrder(int $orderId): ?array {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('SELECT * FROM order_ratings WHERE order_id = ?');
            $stmt->execute([$orderId]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (Exception $e) {
            error_log('Rating get error: ' . $e->getMessage());
            return null;
        }
    }

    public static function getAverageRating(): float {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->query('SELECT AVG(rating) as avg_rating FROM order_ratings');
            $result = $stmt->fetch();
            return $result ? (float)$result['avg_rating'] : 0.0;
        } catch (Exception $e) {
            error_log('Rating average error: ' . $e->getMessage());
            return 0.0;
        }
    }

    public static function getRecentReviews(int $limit = 10): array {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('
                SELECT r.*, u.email as user_email, o.id as order_id
                FROM order_ratings r
                JOIN doctors d ON r.user_id = u.id
                JOIN orders o ON r.order_id = o.id
                WHERE r.review IS NOT NULL AND r.review != ""
                ORDER BY r.created_at DESC
                LIMIT ?
            ');
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Recent reviews error: ' . $e->getMessage());
            return [];
        }
    }
}
