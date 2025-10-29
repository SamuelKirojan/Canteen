<?php
$user = $user ?? [];
$stats = $stats ?? ['total_orders' => 0, 'completed_orders' => 0, 'ongoing_orders' => 0];
$favoriteCount = $favoriteCount ?? 0;
$recentOrders = $recentOrders ?? [];
$error = $error ?? '';
?>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <!-- Profile Sidebar -->
      <div class="col-md-3">
        <div class="card">
          <div class="card-body text-center">
            <div class="mb-3">
              <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                <i class="bi bi-person-fill"></i>
              </div>
            </div>
            <h5 class="mb-1"><?php echo htmlspecialchars($user['email'] ?? 'User'); ?></h5>
            <p class="text-muted small mb-3">Member since <?php echo date('M Y', strtotime($user['created_at'] ?? 'now')); ?></p>
            
            <div class="d-grid gap-2">
              <a href="index.php?r=profile/changePassword" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-key me-1"></i>Change Password
              </a>
              <a href="index.php?r=profile/favorites" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-heart me-1"></i>My Favorites
              </a>
              <a href="index.php?r=profile/notifications" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-bell me-1"></i>Notifications
              </a>
              <a href="index.php?r=order/history" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-clock-history me-1"></i>Order History
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Content -->
      <div class="col-md-9">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <h3 class="mb-4">Profile Overview</h3>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="card border-primary">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-muted mb-1">Total Orders</h6>
                    <h3 class="mb-0"><?php echo (int)$stats['total_orders']; ?></h3>
                  </div>
                  <i class="bi bi-bag-check text-primary" style="font-size: 2rem;"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-success">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 class="mb-0"><?php echo (int)$stats['completed_orders']; ?></h3>
                  </div>
                  <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border-warning">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="text-muted mb-1">Favorites</h6>
                    <h3 class="mb-0"><?php echo $favoriteCount; ?></h3>
                  </div>
                  <i class="bi bi-heart-fill text-warning" style="font-size: 2rem;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Recent Orders</h5>
          </div>
          <div class="card-body p-0">
            <?php if (empty($recentOrders)): ?>
              <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No orders yet</p>
                <a href="index.php?r=menu/index" class="btn btn-primary btn-sm mt-2">Start Ordering</a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Order ID</th>
                      <th>Type</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                      <?php
                        $items = $order['items'] ?? [];
                        $subtotal = 0.0;
                        foreach ($items as $it) { $subtotal += ((float)$it['price']) * (int)$it['qty']; }
                        $deliveryCost = isset($order['delivery_cost']) && $order['delivery_cost'] !== null ? (int)$order['delivery_cost'] : 0;
                        $total = $subtotal + ($order['type'] === 'delivery' ? (float)$deliveryCost : 0);
                        
                        $statusClass = 'warning';
                        if ($order['status'] === 'Ready') $statusClass = 'success';
                        elseif ($order['status'] === 'Packing') $statusClass = 'info';
                        elseif (in_array($order['status'], ['Success', 'Completed'])) $statusClass = 'secondary';
                      ?>
                      <tr>
                        <td class="fw-bold">#<?php echo (int)$order['id']; ?></td>
                        <td>
                          <span class="badge bg-<?php echo $order['type']==='delivery'?'primary':'secondary'; ?>">
                            <?php echo ucfirst($order['type']); ?>
                          </span>
                        </td>
                        <td><?php echo count($items); ?> item(s)</td>
                        <td class="fw-semibold">Rp <?php echo number_format($total * 1000, 0, ',', '.'); ?></td>
                        <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['status']); ?></span></td>
                        <td class="small"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                          <a href="index.php?r=order/info&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
