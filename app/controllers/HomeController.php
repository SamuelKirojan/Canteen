<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/models/Menu.php';

class HomeController extends Controller {
    public function index(): void {
        $this->render('home/landing', ['hideAppLinks' => true]);
    }
}
