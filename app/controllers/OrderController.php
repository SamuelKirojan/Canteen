<?php
require_once APP_ROOT . '/app/core/Controller.php';
require_once APP_ROOT . '/app/models/Order.php';
require_once APP_ROOT . '/app/models/Rating.php';
require_once APP_ROOT . '/app/models/Notification.php';

class OrderController extends Controller {
    private function requireLogin(): ?int {
        if (!empty($_SESSION['user_id'])) return (int)$_SESSION['user_id'];
        if (!empty($_SESSION['admin_id'])) return (int)$_SESSION['admin_id']; // allow admin to place orders, optional
        header('Location: index.php?r=auth/account&t=login');
        return null;
    }

    public function create(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }
        $type = $_POST['type'] ?? 'pickup';
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $deliveryCost = isset($_POST['deliveryCost']) && $_POST['deliveryCost'] !== '' ? (int)$_POST['deliveryCost'] : null;
        $paymentMethod = trim($_POST['paymentMethod'] ?? 'Transfer');
        $itemsJson = $_POST['items'] ?? '[]';
        $items = json_decode($itemsJson, true);
        if (!is_array($items) || count($items) === 0) {
            http_response_code(400);
            echo 'No items';
            return;
        }
        try {
            $orderId = OrderModel::create($uid, $type, $name, $phone, $address, $deliveryCost, $paymentMethod, $items);
        } catch (Throwable $e) {
            http_response_code(500);
            echo 'Error: ' . htmlspecialchars($e->getMessage());
            return;
        }
        header('Location: index.php?r=order/info&id='.$orderId);
    }

    public function history(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;
        $orders = OrderModel::listByUser($uid);
        $this->render('orders/history', ['orders' => $orders, 'hideLandingLinks' => true, 'hideFooter' => true]);
    }

    public function info(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($orderId <= 0) {
            http_response_code(400);
            echo 'Invalid order ID';
            return;
        }
        // Get order and verify ownership
        $order = OrderModel::getById($orderId);
        if (!$order || (int)$order['user_id'] !== $uid) {
            http_response_code(404);
            echo 'Order not found or access denied';
            return;
        }
        $this->render('orders/info', ['order' => $order, 'hideLandingLinks' => true, 'hideFooter' => true]);
    }

    public function ongoing(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;
        
        $orders = OrderModel::listByUser($uid);
        $ongoing = [];
        
        foreach ($orders as $o) {
            $status = $o['status'] ?? '';
            // Only show ongoing orders, exclude completed ones
            if (in_array($status, ['Not Ready', 'Ready', 'Packing'])) {
                $items = $o['items'] ?? [];
                $subtotal = 0.0;
                foreach ($items as $it) { $subtotal += ((float)$it['price']) * (int)$it['qty']; }
                $deliveryCost = isset($o['delivery_cost']) && $o['delivery_cost'] !== null ? (int)$o['delivery_cost'] : 0;
                $total = $subtotal + ($o['type'] === 'delivery' ? (float)$deliveryCost : 0);
                
                $ongoing[] = [
                    'id' => (int)$o['id'],
                    'status' => $status,
                    'type' => ucfirst($o['type']),
                    'item_count' => count($items),
                    'total' => $total
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['orders' => $ongoing]);
    }

    public function status(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;
        
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($orderId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid order ID']);
            return;
        }
        
        $order = OrderModel::getById($orderId);
        if (!$order || (int)$order['user_id'] !== $uid) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => $order['status']]);
    }

    public function cancel(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $reason = trim($_POST['reason'] ?? 'Customer request');

        if ($orderId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid order ID']);
            return;
        }

        try {
            $order = OrderModel::getById($orderId);
            if (!$order || (int)$order['user_id'] !== $uid) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Order not found']);
                return;
            }

            // Check if order can be cancelled (only Not Ready orders within 5 minutes)
            if ($order['status'] !== 'Not Ready') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Order cannot be cancelled']);
                return;
            }

            $createdTime = strtotime($order['created_at']);
            $currentTime = time();
            $minutesElapsed = ($currentTime - $createdTime) / 60;

            if ($minutesElapsed > 5) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Cancellation time limit exceeded (5 minutes)']);
                return;
            }

            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('UPDATE orders SET status = "Cancelled", cancelled_at = NOW(), cancellation_reason = ? WHERE id = ?');
            $stmt->execute([$reason, $orderId]);

            // Send notification
            Notification::create($uid, 'Order Cancelled', "Your order #$orderId has been cancelled.", 'warning', $orderId);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
        } catch (Exception $e) {
            error_log('Order cancel error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to cancel order']);
        }
    }

    public function rate(): void {
        $uid = $this->requireLogin();
        if ($uid === null) return;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $review = trim($_POST['review'] ?? '');

        if ($orderId <= 0 || $rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            return;
        }

        try {
            $order = OrderModel::getById($orderId);
            if (!$order || (int)$order['user_id'] !== $uid) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Order not found']);
                return;
            }

            // Check if order is completed
            if (!in_array($order['status'], ['Success', 'Completed', 'Delivered'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Can only rate completed orders']);
                return;
            }

            $result = Rating::add($orderId, $uid, $rating, $review);

            if ($result) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Rating submitted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to submit rating']);
            }
        } catch (Exception $e) {
            error_log('Order rate error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }
}
