<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/models/Favorite.php';

class FavoriteController extends Controller {
    
    private function requireLogin(): ?int {
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        return (int)$_SESSION['user_id'];
    }

    public function toggle(): void {
        // Clear any previous output
        if (ob_get_level()) ob_end_clean();
        ob_start();
        
        header('Content-Type: application/json');
        $userId = $this->requireLogin();
        if ($userId === null) exit;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $menuId = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;

        if ($menuId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid menu ID']);
            exit;
        }

        try {
            $isFavorite = Favorite::isFavorite($userId, $menuId);
            
            if ($isFavorite) {
                $result = Favorite::remove($userId, $menuId);
                $action = 'removed';
            } else {
                $result = Favorite::add($userId, $menuId);
                $action = 'added';
            }

            // Clear any buffered output
            ob_clean();
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'action' => $action,
                    'is_favorite' => !$isFavorite
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update favorite']);
            }
            ob_end_flush();
            exit;
        } catch (Exception $e) {
            error_log('Favorite toggle error: ' . $e->getMessage());
            ob_clean();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
            ob_end_flush();
            exit;
        }
    }

    public function list(): void {
        $userId = $this->requireLogin();
        if ($userId === null) return;

        try {
            $favorites = Favorite::getByUser($userId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'favorites' => $favorites]);
        } catch (Exception $e) {
            error_log('Favorite list error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }
}
