<?php
class Router {
    public function dispatch(): void {
        $route = isset($_GET['r']) ? trim($_GET['r'], '/') : 'home/index';
        [$controllerName, $action] = array_pad(explode('/', $route, 2), 2, null);
        $controllerName = $controllerName ? ucfirst(strtolower($controllerName)) . 'Controller' : 'HomeController';
        $action = $action ?: 'index';

        $controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(404);
            echo 'Controller not found';
            return;
        }
        require_once $controllerFile;
        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo 'Controller class missing';
            return;
        }
        $controller = new $controllerName();
        if (!method_exists($controller, $action)) {
            http_response_code(404);
            echo 'Action not found';
            return;
        }
        $controller->$action();
    }
}
