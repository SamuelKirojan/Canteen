<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/models/User.php';
require_once APP_ROOT . '/app/models/Admin.php';

class AuthController extends Controller {
    public function account(): void {
        $tab = $_GET['t'] ?? 'login';
        $this->render('auth/account', ['tab' => $tab]);
    }
    
    public function login(): void {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = 'user';
                header('Location: index.php?r=menu/index');
                exit;
            }
            $error = 'Invalid email or password.';
        }
        $this->render('auth/account', ['tab' => 'login', 'error' => $error]);
    }

    public function signup(): void {
        $error = null; $success = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif (User::findByEmail($email)) {
                $error = 'Email already registered.';
            } else {
                $id = User::create($email, $password);
                // Auto-login then go to menu page
                $_SESSION['user_id'] = (int)$id;
                $_SESSION['user_email'] = $email;
                $_SESSION['role'] = 'user';
                header('Location: index.php?r=menu/index');
                exit;
            }
        }
        $this->render('auth/account', ['tab' => 'signup', 'error' => $error, 'success' => $success]);
    }

    public function admin(): void {
        // Redirect to new admin login
        header('Location: index.php?r=admin/login');
        exit;
    }

    public function logout(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: index.php?r=home/index');
        exit;
    }
}
