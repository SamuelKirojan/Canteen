<?php
require_once APP_ROOT . '/app/core/Database.php';

class OrderModel {
    public static function create(int $userId, string $type, string $name, string $phone, string $address, ?int $deliveryCost, string $paymentMethod, array $items): int {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        try {
            // Validate payload
            $clean = [];
            foreach ($items as $it) {
                $mid = isset($it['id']) ? (int)$it['id'] : 0;
                $qty = isset($it['qty']) ? (int)$it['qty'] : 0;
                if ($mid <= 0 || $qty <= 0) {
                    throw new Exception('Invalid item payload.');
                }
                if (isset($clean[$mid])) { $clean[$mid] += $qty; } else { $clean[$mid] = $qty; }
            }

            // Lock rows and verify stock
            $ids = array_keys($clean);
            $in = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT id, name, price, stock FROM menu WHERE id IN ($in) FOR UPDATE");
            $stmt->execute($ids);
            $rows = $stmt->fetchAll();
            if (count($rows) !== count($ids)) {
                throw new Exception('Some items are no longer available.');
            }

            $byId = [];
            foreach ($rows as $row) { $byId[(int)$row['id']] = $row; }
            foreach ($clean as $mid => $qty) {
                if (!isset($byId[$mid])) { throw new Exception('Item not found: '.$mid); }
                if ((int)$byId[$mid]['stock'] < $qty) { throw new Exception('Insufficient stock for: '.$byId[$mid]['name']); }
            }

            // Create order
            $stmtOrder = $pdo->prepare('INSERT INTO orders (user_id, type, name, phone, address, delivery_cost, payment_method, status, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())');
            $stmtOrder->execute([$userId, $type, $name, $phone, $address, $deliveryCost, $paymentMethod, 'Not Ready']);
            $orderId = (int)$pdo->lastInsertId();

            // Insert items using server-side name/price and decrement stock
            $stmtLine = $pdo->prepare('INSERT INTO order_items (order_id, menu_id, name, price, qty) VALUES (?,?,?,?,?)');
            $stmtUpdate = $pdo->prepare('UPDATE menu SET stock = stock - ? WHERE id = ?');
            foreach ($clean as $mid => $qty) {
                $m = $byId[$mid];
                $stmtLine->execute([$orderId, $mid, $m['name'], (float)$m['price'], $qty]);
                $stmtUpdate->execute([$qty, $mid]);
            }

            $pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function listByUser(int $userId): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();
        if (!$orders) return [];
        $ids = array_column($orders, 'id');
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt2 = $pdo->prepare("SELECT * FROM order_items WHERE order_id IN ($in) ORDER BY id");
        $stmt2->execute($ids);
        $items = $stmt2->fetchAll();
        $by = [];
        foreach ($items as $it) { $by[$it['order_id']][] = $it; }
        foreach ($orders as &$o) { $o['items'] = $by[$o['id']] ?? []; }
        return $orders;
    }

    public static function getById(int $orderId): ?array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) return null;
        $stmt2 = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id');
        $stmt2->execute([$orderId]);
        $order['items'] = $stmt2->fetchAll();
        return $order;
    }
}
