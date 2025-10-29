<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/models/Order.php';
require_once APP_ROOT . '/app/models/Menu.php';
require_once APP_ROOT . '/app/models/Notification.php';

class AdminController extends Controller {
    
    private function requireAdmin(): ?int {
        if (empty($_SESSION['admin_id'])) {
            header('Location: index.php?r=admin/login');
            return null;
        }
        return (int)$_SESSION['admin_id'];
    }

    public function login(): void {
        if (!empty($_SESSION['admin_id'])) {
            header('Location: index.php?r=admin/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->render('admin/login', ['error' => 'Email and password are required', 'hideChrome' => true]);
                return;
            }

            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare('SELECT id, email, password, name FROM admins WHERE email = ?');
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if (!$admin) {
                    error_log('Admin login: No admin found with email: ' . $email);
                    $this->render('admin/login', ['error' => 'Invalid email or password', 'hideChrome' => true]);
                    return;
                }
                
                if (!password_verify($password, $admin['password'])) {
                    error_log('Admin login: Password verification failed for: ' . $email);
                    $this->render('admin/login', ['error' => 'Invalid email or password', 'hideChrome' => true]);
                    return;
                }

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['name'];
                
                header('Location: index.php?r=admin/dashboard');
            } catch (Exception $e) {
                error_log('Admin login error: ' . $e->getMessage());
                $this->render('admin/login', ['error' => 'Login failed. Please try again.', 'hideChrome' => true]);
            }
            return;
        }

        $this->render('admin/login', ['hideChrome' => true]);
    }

    public function logout(): void {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        header('Location: index.php?r=admin/login');
    }

    public function signup(): void {
        if (!empty($_SESSION['admin_id'])) {
            header('Location: index.php?r=admin/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                $this->render('admin/signup', ['error' => 'All fields are required', 'hideChrome' => true]);
                return;
            }

            if ($password !== $confirmPassword) {
                $this->render('admin/signup', ['error' => 'Passwords do not match', 'hideChrome' => true]);
                return;
            }

            if (strlen($password) < 6) {
                $this->render('admin/signup', ['error' => 'Password must be at least 6 characters', 'hideChrome' => true]);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->render('admin/signup', ['error' => 'Invalid email address', 'hideChrome' => true]);
                return;
            }

            try {
                $pdo = Database::getInstance();
                
                // Check if email already exists
                $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $this->render('admin/signup', ['error' => 'Email already registered', 'hideChrome' => true]);
                    return;
                }

                // Create admin account
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO admins (email, password, name, created_at) VALUES (?, ?, ?, NOW())');
                $stmt->execute([$email, $hashedPassword, $name]);

                error_log('New admin created: ' . $email);
                $this->render('admin/signup', [
                    'success' => 'Admin account created successfully! You can now login.',
                    'hideChrome' => true
                ]);
            } catch (Exception $e) {
                error_log('Admin signup error: ' . $e->getMessage());
                $this->render('admin/signup', ['error' => 'Signup failed. Please try again.', 'hideChrome' => true]);
            }
            return;
        }

        $this->render('admin/signup', ['hideChrome' => true]);
    }

    public function dashboard(): void {
        $adminId = $this->requireAdmin();
        if ($adminId === null) return;

        try {
            $pdo = Database::getInstance();
            
            // Get all orders with items, ordered by status priority and created_at
            $stmt = $pdo->prepare("
                SELECT o.*, 
                    CASE 
                        WHEN o.status = 'Not Ready' THEN 1
                        WHEN o.status = 'Ready' THEN 2
                        WHEN o.status = 'Packing' THEN 3
                        ELSE 4
                    END as priority
                FROM orders o
                ORDER BY priority ASC, o.created_at DESC
            ");
            $stmt->execute();
            $orders = $stmt->fetchAll();

            // Get items for each order
            if (!empty($orders)) {
                $ids = array_column($orders, 'id');
                $in = implode(',', array_fill(0, count($ids), '?'));
                $stmt2 = $pdo->prepare("SELECT * FROM order_items WHERE order_id IN ($in) ORDER BY id");
                $stmt2->execute($ids);
                $items = $stmt2->fetchAll();
                
                $by = [];
                foreach ($items as $it) { $by[$it['order_id']][] = $it; }
                foreach ($orders as &$o) { $o['items'] = $by[$o['id']] ?? []; }
            }

            $this->render('admin/dashboard', [
                'orders' => $orders,
                'hideLandingLinks' => true,
                'hideAppLinks' => true,
                'hideFooter' => true
            ]);
        } catch (Exception $e) {
            error_log('Admin dashboard error: ' . $e->getMessage());
            $this->render('admin/dashboard', [
                'orders' => [],
                'error' => 'Failed to load orders',
                'hideLandingLinks' => true,
                'hideAppLinks' => true,
                'hideFooter' => true
            ]);
        }
    }

    public function updateStatus(): void {
        $adminId = $this->requireAdmin();
        if ($adminId === null) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $newStatus = trim($_POST['status'] ?? '');

        if ($orderId <= 0 || empty($newStatus)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid order ID or status']);
            return;
        }

        $allowedStatuses = ['Not Ready', 'Ready', 'Packing', 'Success'];
        if (!in_array($newStatus, $allowedStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status value']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            
            // Get old status
            $stmt = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
            $stmt->execute([$orderId]);
            $order = $stmt->fetch();
            $oldStatus = $order ? $order['status'] : '';
            
            // Update status
            $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $stmt->execute([$newStatus, $orderId]);

            // Send notification to user
            if ($oldStatus !== $newStatus) {
                Notification::sendOrderStatusNotification($orderId, $oldStatus, $newStatus);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'status' => $newStatus]);
        } catch (Exception $e) {
            error_log('Status update error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update status']);
        }
    }

    public function menu(): void {
        $adminId = $this->requireAdmin();
        if ($adminId === null) return;

        try {
            $items = Menu::all();
            $this->render('admin/menu', [
                'items' => $items,
                'hideLandingLinks' => true,
                'hideAppLinks' => true,
                'hideFooter' => true
            ]);
        } catch (Exception $e) {
            error_log('Admin menu error: ' . $e->getMessage());
            $this->render('admin/menu', [
                'items' => [],
                'error' => 'Failed to load menu items',
                'hideLandingLinks' => true,
                'hideAppLinks' => true,
                'hideFooter' => true
            ]);
        }
    }

    public function updateMenu(): void {
        $adminId = $this->requireAdmin();
        if ($adminId === null) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $menuId = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
        $field = trim($_POST['field'] ?? '');
        $value = trim($_POST['value'] ?? '');

        if ($menuId <= 0 || empty($field)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            return;
        }

        $allowedFields = ['name', 'description', 'price', 'stock'];
        if (!in_array($field, $allowedFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid field']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            
            // Validate value based on field
            if ($field === 'price' || $field === 'stock') {
                if (!is_numeric($value) || $value < 0) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid numeric value']);
                    return;
                }
            }

            $stmt = $pdo->prepare("UPDATE menu SET $field = ? WHERE id = ?");
            $stmt->execute([$value, $menuId]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'field' => $field, 'value' => $value]);
        } catch (Exception $e) {
            error_log('Menu update error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update menu']);
        }
    }
}
