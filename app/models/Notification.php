<?php
class Notification {
    public static function create(int $userId, string $title, string $message, string $type = 'info', ?int $orderId = null): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('
                INSERT INTO notifications (user_id, order_id, title, message, type, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ');
            return $stmt->execute([$userId, $orderId, $title, $message, $type]);
        } catch (Exception $e) {
            error_log('Notification create error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getByUser(int $userId, int $limit = 20): array {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('
                SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?
            ');
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('Notification get error: ' . $e->getMessage());
            return [];
        }
    }

    public static function getUnreadCount(int $userId): int {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0');
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result ? (int)$result['count'] : 0;
        } catch (Exception $e) {
            error_log('Notification count error: ' . $e->getMessage());
            return 0;
        }
    }

    public static function markAsRead(int $notificationId): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
            return $stmt->execute([$notificationId]);
        } catch (Exception $e) {
            error_log('Notification mark read error: ' . $e->getMessage());
            return false;
        }
    }

    public static function markAllAsRead(int $userId): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log('Notification mark all read error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendOrderStatusNotification(int $orderId, string $oldStatus, string $newStatus): bool {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('SELECT user_id FROM orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();
            
            if (!$order) return false;

            $messages = [
                'Ready' => 'Your order is ready for pickup!',
                'Packing' => 'Your order is being packed for delivery.',
                'Success' => 'Your order has been completed. Thank you!',
                'Delivered' => 'Your order has been delivered. Enjoy your meal!'
            ];

            $title = "Order #$orderId Status Update";
            $message = $messages[$newStatus] ?? "Your order status has been updated to $newStatus";
            $type = $newStatus === 'Success' ? 'success' : 'info';

            return self::create($order['user_id'], $title, $message, $type, $orderId);
        } catch (Exception $e) {
            error_log('Order notification error: ' . $e->getMessage());
            return false;
        }
    }
}
