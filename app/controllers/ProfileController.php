<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/models/User.php';
require_once APP_ROOT . '/app/models/Order.php';
require_once APP_ROOT . '/app/models/Favorite.php';
require_once APP_ROOT . '/app/models/Rating.php';
require_once APP_ROOT . '/app/models/Notification.php';

class ProfileController extends Controller {
    
    private function requireLogin(): ?int {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?r=auth/account');
            return null;
        }
        return (int)$_SESSION['user_id'];
    }

    public function index(): void {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        try {
            $pdo = Database::getInstance();
            
            // Get user info
            $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            // Get order stats
            $stmt = $pdo->prepare('
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status IN ("Success", "Completed", "Delivered") THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status IN ("Not Ready", "Ready", "Packing") THEN 1 ELSE 0 END) as ongoing_orders
                FROM orders 
                WHERE user_id = ?
            ');
            $stmt->execute([$userId]);
            $stats = $stmt->fetch();

            // Get favorite count
            $favoriteCount = count(Favorite::getUserFavoriteIds($userId));

            // Get recent orders
            $recentOrders = OrderModel::listByUser($userId, 5);

            $this->render('profile/index', [
                'user' => $user,
                'stats' => $stats,
                'favoriteCount' => $favoriteCount,
                'recentOrders' => $recentOrders,
                'hideLandingLinks' => true
            ]);
        } catch (Exception $e) {
            error_log('Profile error: ' . $e->getMessage());
            $this->render('profile/index', [
                'error' => 'Failed to load profile',
                'hideLandingLinks' => true
            ]);
        }
    }

    public function changePassword(): void {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare('SELECT password_hash FROM doctors WHERE id = ?');
                $stmt->execute([$userId]);
                $user = $stmt->fetch();

                if (!password_verify($currentPassword, $user['password_hash'])) {
                    $this->render('profile/change-password', [
                        'error' => 'Current password is incorrect',
                        'hideLandingLinks' => true
                    ]);
                    return;
                }

                if ($newPassword !== $confirmPassword) {
                    $this->render('profile/change-password', [
                        'error' => 'New passwords do not match',
                        'hideLandingLinks' => true
                    ]);
                    return;
                }

                if (strlen($newPassword) < 6) {
                    $this->render('profile/change-password', [
                        'error' => 'Password must be at least 6 characters',
                        'hideLandingLinks' => true
                    ]);
                    return;
                }

                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE doctors SET password_hash = ? WHERE id = ?');
                $stmt->execute([$hashedPassword, $userId]);

                $this->render('profile/change-password', [
                    'success' => 'Password changed successfully!',
                    'hideLandingLinks' => true
                ]);
            } catch (Exception $e) {
                error_log('Change password error: ' . $e->getMessage());
                $this->render('profile/change-password', [
                    'error' => 'Failed to change password',
                    'hideLandingLinks' => true
                ]);
            }
            return;
        }

        $this->render('profile/change-password', ['hideLandingLinks' => true]);
    }

    public function favorites(): void {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        $favorites = Favorite::getByUser($userId);
        $this->render('profile/favorites', [
            'favorites' => $favorites,
            'hideLandingLinks' => true
        ]);
    }

    public function notifications(): void {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        $notifications = Notification::getByUser($userId, 50);
        $this->render('profile/notifications', [
            'notifications' => $notifications,
            'hideLandingLinks' => true
        ]);
    }
}
