<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/models/Menu.php';
require_once APP_ROOT . '/app/models/Favorite.php';

class MenuController extends Controller {
    public function index(): void {
        if (empty($_SESSION['user_id']) && empty($_SESSION['admin_id'])) {
            header('Location: index.php?r=auth/account&t=login');
            return;
        }
        try {
            $items = Menu::all();
            
            // Get user's favorite IDs if logged in as user
            $favoriteIds = [];
            if (!empty($_SESSION['user_id'])) {
                $favoriteIds = Favorite::getUserFavoriteIds((int)$_SESSION['user_id']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'DB error: ' . htmlspecialchars($e->getMessage());
            return;
        }
        $this->render('menu/shop', [
            'items' => $items, 
            'favoriteIds' => $favoriteIds,
            'hideLandingLinks' => true, 
            'hideFooter' => true
        ]);
    }
}
