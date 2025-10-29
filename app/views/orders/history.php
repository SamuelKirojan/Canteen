<?php
$orders = $orders ?? [];
?>
<section class="py-4">
  <div class="container">
    <?php 
      // Separate ongoing and completed orders
      $ongoing = [];
      $completed = [];
      foreach ($orders as $o) {
        $status = $o['status'] ?? '';
        if (in_array($status, ['Not Ready', 'Ready', 'Packing'])) {
          $ongoing[] = $o;
        } elseif (in_array($status, ['Success', 'Completed', 'Delivered'])) {
          $completed[] = $o;
        }
      }
    ?>

    <?php if (!empty($ongoing)): ?>
      <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-clock-history me-2" style="font-size:1.5rem;"></i>
        <div class="flex-grow-1">
          <h5 class="mb-1">You have <?php echo count($ongoing); ?> ongoing order(s)</h5>
          <p class="mb-0 small">Click on an order below to view real-time status</p>
        </div>
      </div>

      <h4 class="mb-3">Ongoing Orders</h4>
      <div class="vstack gap-3 mb-4">
        <?php foreach ($ongoing as $o): ?>
          <?php 
            $created = htmlspecialchars($o['created_at'] ?? '');
            $status = htmlspecialchars($o['status'] ?? '');
            $type = htmlspecialchars($o['type'] ?? '');
            $items = $o['items'] ?? [];
            $subtotal = 0.0; foreach ($items as $it) { $subtotal += ((float)$it['price']) * (int)$it['qty']; }
            $deliveryCost = isset($o['delivery_cost']) && $o['delivery_cost'] !== null ? (int)$o['delivery_cost'] : null;
            $total = $subtotal + ($type==='delivery' ? (float)($deliveryCost ?? 0) : 0);
            $statusBg = $status === 'Not Ready' ? 'bg-warning' : ($status === 'Ready' ? 'bg-success' : 'bg-info');
          ?>
          <a href="index.php?r=order/info&id=<?php echo (int)$o['id']; ?>" class="card text-decoration-none border-primary" style="border-width:2px;">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">Order #<?php echo (int)$o['id']; ?></div>
                <span class="badge <?php echo $statusBg; ?> text-uppercase"><?php echo $status; ?></span>
              </div>
              <div class="text-muted small mb-2">Placed: <?php echo $created; ?> • <?php echo ucfirst($type); ?></div>
              <div class="d-flex justify-content-between align-items-center">
                <div class="small"><?php echo count($items); ?> item(s)</div>
                <div class="fw-semibold">Rp <?php echo number_format($total * 1000, 0, ',', '.'); ?></div>
              </div>
              <div class="mt-2 text-primary small">
                <i class="bi bi-arrow-right-circle me-1"></i>Click to view real-time status
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-3">
      <h3 class="mb-0">Order History</h3>
      <div class="text-muted small">Your completed orders</div>
    </div>

    <?php if (empty($completed) && empty($ongoing)): ?>
      <div class="alert alert-light border">No orders yet.</div>
    <?php elseif (!empty($completed)): ?>
      <div class="vstack gap-3">
        <?php foreach ($completed as $o): ?>
          <?php 
            $created = htmlspecialchars($o['created_at'] ?? '');
            $status = htmlspecialchars($o['status'] ?? '');
            $type = htmlspecialchars($o['type'] ?? '');
            $name = htmlspecialchars($o['name'] ?? '');
            $phone = htmlspecialchars($o['phone'] ?? '');
            $address = htmlspecialchars($o['address'] ?? '');
            $deliveryCost = isset($o['delivery_cost']) && $o['delivery_cost'] !== null ? (int)$o['delivery_cost'] : null;
            $items = $o['items'] ?? [];
            $subtotal = 0.0; foreach ($items as $it) { $subtotal += ((float)$it['price']) * (int)$it['qty']; }
            $total = $subtotal + ($type==='delivery' ? (float)($deliveryCost ?? 0) : 0);
          ?>
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">Order #<?php echo (int)$o['id']; ?></div>
                <div class="badge bg-secondary text-uppercase"><?php echo $status; ?></div>
              </div>
              <div class="text-muted small mb-2">Placed: <?php echo $created; ?> • <?php echo ucfirst($type); ?></div>
              <div class="row g-2 mb-2 small">
                <div class="col-auto"><strong>Name:</strong> <?php echo $name; ?></div>
                <?php if ($type==='delivery'): ?>
                  <div class="col-auto"><strong>Phone:</strong> <?php echo $phone ?: '-'; ?></div>
                  <div class="col-12"><strong>Address:</strong> <?php echo $address ?: '-'; ?></div>
                <?php endif; ?>
              </div>
              <div class="list-group mb-2">
                <?php foreach ($items as $it): ?>
                  <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div><?php echo htmlspecialchars($it['name']); ?> x <?php echo (int)$it['qty']; ?></div>
                    <div>Rp <?php echo number_format((float)$it['price'] * 1000, 0, ',', '.'); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="border-top pt-2">
                <div class="d-flex justify-content-between small">
                  <div>Subtotal</div>
                  <div>Rp <?php echo number_format($subtotal * 1000, 0, ',', '.'); ?></div>
                </div>
                <?php if ($type==='delivery'): ?>
                  <div class="d-flex justify-content-between small">
                    <div>Delivery</div>
                    <div>Rp <?php echo number_format(((float)($deliveryCost ?? 0)) * 1000, 0, ',', '.'); ?></div>
                  </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between fw-semibold">
                  <div>Total</div>
                  <div>Rp <?php echo number_format($total * 1000, 0, ',', '.'); ?></div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

