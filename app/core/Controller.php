<?php
class Controller {
    protected function render(string $view, array $params = []): void {
        extract($params, EXTR_SKIP);
        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view);
            return;
        }
        
        // Check if we should skip the layout (for standalone pages like login)
        if (isset($params['hideChrome']) && $params['hideChrome'] === true) {
            include $viewFile;
            return;
        }
        
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        include APP_ROOT . '/app/views/layout.php';
    }
}
