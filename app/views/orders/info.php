<?php
$order = $order ?? null;
if (!$order) {
    echo '<div class="alert alert-warning">Order not found.</div>';
    return;
}
$items = $order['items'] ?? [];
$subtotal = 0.0;
foreach ($items as $it) { $subtotal += ((float)$it['price']) * (int)$it['qty']; }
$deliveryCost = isset($order['delivery_cost']) && $order['delivery_cost'] !== null ? (int)$order['delivery_cost'] : 0;
$total = $subtotal + ($order['type']==='delivery' ? (float)$deliveryCost : 0);
?>
<section class="py-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="text-center mb-4">
              <div class="mb-3">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
              </div>
              <h3 class="mb-2">Order Placed Successfully!</h3>
              <p class="text-muted">Your order is being prepared</p>
            </div>

            <div class="border-top pt-3 mb-3">
              <div class="row g-2 mb-2">
                <div class="col-6"><strong>Order ID:</strong></div>
                <div class="col-6 text-end">#<?php echo (int)$order['id']; ?></div>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-6"><strong>Status:</strong></div>
                <div class="col-6 text-end">
                  <?php 
                    $status = htmlspecialchars($order['status']);
                    $badgeClass = 'bg-warning text-dark';
                    if ($status === 'Success' || $status === 'Completed') {
                      $badgeClass = 'bg-success';
                    } elseif ($status === 'Ready') {
                      $badgeClass = 'bg-success';
                    } elseif ($status === 'Packing' || $status === 'Delivered') {
                      $badgeClass = 'bg-info';
                    }
                  ?>
                  <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                </div>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-6"><strong>Payment Method:</strong></div>
                <div class="col-6 text-end"><?php echo htmlspecialchars($order['payment_method'] ?? 'Transfer'); ?></div>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-6"><strong>Order Type:</strong></div>
                <div class="col-6 text-end"><?php echo ucfirst(htmlspecialchars($order['type'])); ?></div>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-6"><strong>Name:</strong></div>
                <div class="col-6 text-end"><?php echo htmlspecialchars($order['name']); ?></div>
              </div>
              <?php if ($order['type'] === 'delivery'): ?>
                <div class="row g-2 mb-2">
                  <div class="col-6"><strong>Phone:</strong></div>
                  <div class="col-6 text-end"><?php echo htmlspecialchars($order['phone'] ?: '-'); ?></div>
                </div>
                <div class="row g-2 mb-2">
                  <div class="col-6"><strong>Address:</strong></div>
                  <div class="col-6 text-end"><?php echo htmlspecialchars($order['address'] ?: '-'); ?></div>
                </div>
              <?php endif; ?>
              <div class="row g-2">
                <div class="col-6"><strong>Placed:</strong></div>
                <div class="col-6 text-end"><?php echo htmlspecialchars($order['created_at']); ?></div>
              </div>
            </div>

            <div class="border-top pt-3 mb-3">
              <h6 class="mb-3">Order Items</h6>
              <div class="list-group mb-3">
                <?php foreach ($items as $it): ?>
                  <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <div class="fw-semibold"><?php echo htmlspecialchars($it['name']); ?></div>
                      <div class="small text-muted">Qty: <?php echo (int)$it['qty']; ?> × Rp <?php echo number_format((float)$it['price'] * 1000, 0, ',', '.'); ?></div>
                    </div>
                    <div class="fw-semibold">Rp <?php echo number_format((float)$it['price'] * (int)$it['qty'] * 1000, 0, ',', '.'); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="border-top pt-3">
              <div class="d-flex justify-content-between mb-2">
                <div>Subtotal</div>
                <div>Rp <?php echo number_format($subtotal * 1000, 0, ',', '.'); ?></div>
              </div>
              <?php if ($order['type'] === 'delivery' && $deliveryCost > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                  <div>Delivery Cost</div>
                  <div>Rp <?php echo number_format($deliveryCost * 1000, 0, ',', '.'); ?></div>
                </div>
              <?php endif; ?>
              <div class="d-flex justify-content-between fw-bold fs-5">
                <div>Total</div>
                <div>Rp <?php echo number_format($total * 1000, 0, ',', '.'); ?></div>
              </div>
            </div>

            <?php if (!in_array($order['status'], ['Success', 'Completed', 'Delivered'])): ?>
            <div class="border-top pt-3 mt-3">
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                <?php if ($order['type'] === 'delivery'): ?>
                  <strong>Estimated delivery time:</strong> 30-45 minutes
                <?php else: ?>
                  <strong>Estimated ready time:</strong> 15-20 minutes
                <?php endif; ?>
              </div>
            </div>
            <?php else: ?>
            <div class="border-top pt-3 mt-3">
              <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Order Completed!</strong> Thank you for your order.
              </div>
            </div>
            <?php endif; ?>

            <div class="d-grid gap-2 mt-4">
              <a href="index.php?r=order/history" class="btn btn-outline-primary">View Order History</a>
              <a href="index.php?r=menu/index" class="btn btn-primary">Back to Menu</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function(){
  const currentStatus = '<?php echo $order['status']; ?>';
  
  // Don't poll if order is already completed
  if(['Success', 'Completed', 'Delivered'].includes(currentStatus)){
    console.log('Order completed, no polling needed');
    return;
  }
  
  // Auto-refresh status every 10 seconds for ongoing orders
  let refreshCount = 0;
  const maxRefresh = 30; // Stop after 5 minutes
  
  function checkStatus(){
    if(refreshCount >= maxRefresh) return;
    refreshCount++;
    
    fetch('index.php?r=order/status&id=<?php echo (int)$order['id']; ?>')
      .then(r=>r.json())
      .then(data=>{
        if(data.status){
          const badge = document.querySelector('.badge');
          if(badge) {
            badge.textContent = data.status;
            // Update badge color
            badge.className = 'badge';
            if(data.status === 'Success' || data.status === 'Completed'){
              badge.classList.add('bg-success');
            } else if(data.status === 'Ready'){
              badge.classList.add('bg-success');
            } else if(data.status === 'Packing' || data.status === 'Delivered'){
              badge.classList.add('bg-info');
            } else {
              badge.classList.add('bg-warning', 'text-dark');
            }
          }
          
          // If completed, stop polling and reload page to show completion message
          if(['Success', 'Completed', 'Delivered'].includes(data.status)){
            clearInterval(statusInterval);
            setTimeout(() => location.reload(), 1000);
          }
        }
      })
      .catch(e=>console.error('Status check failed:', e));
  }
  
  // Poll every 10 seconds
  const statusInterval = setInterval(checkStatus, 10000);
})();
</script>
