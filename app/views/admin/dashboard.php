<?php
$orders = $orders ?? [];
$error = $error ?? '';
$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Separate orders by status
$notReady = [];
$ready = [];
$packing = [];
$completed = [];

foreach ($orders as $o) {
    $status = $o['status'] ?? '';
    if ($status === 'Not Ready') {
        $notReady[] = $o;
    } elseif ($status === 'Ready') {
        $ready[] = $o;
    } elseif ($status === 'Packing') {
        $packing[] = $o;
    } else {
        $completed[] = $o;
    }
}
?>

<section class="py-4">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="mb-1">test dashboard</h2>
        <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($adminName); ?>!</p>
      </div>
      <div>
        <a href="index.php?r=admin/menu" class="btn btn-outline-primary me-2">
          <i class="bi bi-card-list me-1"></i>Manage Menu
        </a>
        <a href="index.php?r=admin/logout" class="btn btn-outline-danger">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-warning">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Not Ready</h6>
                <h3 class="mb-0"><?php echo count($notReady); ?></h3>
              </div>
              <i class="bi bi-clock-history text-warning" style="font-size:2rem;"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-success">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Ready</h6>
                <h3 class="mb-0"><?php echo count($ready); ?></h3>
              </div>
              <i class="bi bi-check-circle text-success" style="font-size:2rem;"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-info">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Packing</h6>
                <h3 class="mb-0"><?php echo count($packing); ?></h3>
              </div>
              <i class="bi bi-box-seam text-info" style="font-size:2rem;"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-secondary">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-1">Completed</h6>
                <h3 class="mb-0"><?php echo count($completed); ?></h3>
              </div>
              <i class="bi bi-archive text-secondary" style="font-size:2rem;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Queue -->
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Order Queue</h5>
      </div>
      <div class="card-body p-0">
        <?php if (empty($orders)): ?>
          <div class="p-4 text-center text-muted">
            <i class="bi bi-inbox" style="font-size:3rem;"></i>
            <p class="mt-2 mb-0">No orders yet</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Type</th>
                  <th>Payment</th>
                  <th>Items</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $o): ?>
                  <?php
                    $items = $o['items'] ?? [];
                    $subtotal = 0.0;
                    foreach ($items as $it) { $subtotal += ((float)$it['price']) * (int)$it['qty']; }
                    $deliveryCost = isset($o['delivery_cost']) && $o['delivery_cost'] !== null ? (int)$o['delivery_cost'] : 0;
                    $total = $subtotal + ($o['type'] === 'delivery' ? (float)$deliveryCost : 0);
                    
                    $statusClass = 'warning';
                    if ($o['status'] === 'Ready') $statusClass = 'success';
                    elseif ($o['status'] === 'Packing') $statusClass = 'info';
                    elseif (in_array($o['status'], ['Success', 'Completed'])) $statusClass = 'secondary';
                  ?>
                  <tr data-order-id="<?php echo (int)$o['id']; ?>">
                    <td class="fw-bold">#<?php echo (int)$o['id']; ?></td>
                    <td>
                      <div><?php echo htmlspecialchars($o['name']); ?></div>
                      <?php if ($o['type'] === 'delivery' && !empty($o['phone'])): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($o['phone']); ?></small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-<?php echo $o['type']==='delivery'?'primary':'secondary'; ?>">
                        <?php echo ucfirst($o['type']); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($o['payment_method'] ?? 'Transfer'); ?></td>
                    <td>
                      <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#items-<?php echo $o['id']; ?>">
                        <?php echo count($items); ?> item(s)
                      </button>
                      <div class="collapse mt-2" id="items-<?php echo $o['id']; ?>">
                        <ul class="list-unstyled small mb-0">
                          <?php foreach ($items as $it): ?>
                            <li><?php echo htmlspecialchars($it['name']); ?> x<?php echo (int)$it['qty']; ?></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    </td>
                    <td class="fw-semibold">Rp <?php echo number_format($total * 1000, 0, ',', '.'); ?></td>
                    <td>
                      <span class="badge bg-<?php echo $statusClass; ?> status-badge">
                        <?php echo htmlspecialchars($o['status']); ?>
                      </span>
                    </td>
                    <td class="small text-muted"><?php echo htmlspecialchars($o['created_at']); ?></td>
                    <td>
                      <?php if (!in_array($o['status'], ['Success', 'Completed'])): ?>
                        <div class="btn-group btn-group-sm">
                          <?php if ($o['status'] === 'Not Ready'): ?>
                            <?php if ($o['type'] === 'pickup'): ?>
                              <button class="btn btn-success btn-update-status" data-order-id="<?php echo $o['id']; ?>" data-status="Ready">
                                <i class="bi bi-check"></i> Ready
                              </button>
                            <?php else: ?>
                              <button class="btn btn-info btn-update-status" data-order-id="<?php echo $o['id']; ?>" data-status="Packing">
                                <i class="bi bi-box"></i> Pack
                              </button>
                            <?php endif; ?>
                          <?php elseif ($o['status'] === 'Ready' || $o['status'] === 'Packing'): ?>
                            <button class="btn btn-primary btn-update-status" data-order-id="<?php echo $o['id']; ?>" data-status="Success">
                              <i class="bi bi-check-circle"></i> Complete
                            </button>
                          <?php endif; ?>
                        </div>
                      <?php else: ?>
                        <span class="text-muted small">Completed</span>
                      <?php endif; ?>
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
</section>

<!-- Toast Container -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index:1090;">
  <div id="toastContainer"></div>
</div>

<style>
  .custom-toast {
    min-width: 300px;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
  }
</style>

<script>
(function(){
  console.log('Admin Dashboard initialized');
  
  function showToast(message, type='success'){
    console.log('Toast:', type, message);
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast custom-toast align-items-center text-bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    
    const icon = type === 'success' ? '✓' : (type === 'danger' ? '✕' : 'ℹ');
    
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <strong style="font-size:1.2rem;margin-right:8px;">${icon}</strong>
          ${message}
        </div>
        <button type="button" class="btn-close ${type==='warning'?'':'btn-close-white'} me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  // Status update handlers
  document.querySelectorAll('.btn-update-status').forEach(btn => {
    btn.addEventListener('click', function(){
      const orderId = this.dataset.orderId;
      const newStatus = this.dataset.status;
      
      console.log('Updating order', orderId, 'to status:', newStatus);
      
      if(!confirm(`Change order #${orderId} to ${newStatus}?`)) return;
      
      const originalBtn = this;
      originalBtn.disabled = true;
      const originalText = originalBtn.innerHTML;
      originalBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
      
      const formData = new FormData();
      formData.append('order_id', orderId);
      formData.append('status', newStatus);
      
      fetch('index.php?r=admin/updateStatus', {
        method: 'POST',
        body: formData
      })
      .then(r => {
        console.log('Response status:', r.status);
        return r.json();
      })
      .then(data => {
        console.log('Response data:', data);
        if(data.success){
          showToast(`Order #${orderId} updated to ${newStatus}`, 'success');
          
          // Update badge
          const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
          if(row){
            const badge = row.querySelector('.status-badge');
            if(badge){
              badge.textContent = newStatus;
              badge.className = 'badge status-badge';
              if(newStatus === 'Ready') badge.classList.add('bg-success');
              else if(newStatus === 'Packing') badge.classList.add('bg-info');
              else if(newStatus === 'Success') badge.classList.add('bg-secondary');
              else badge.classList.add('bg-warning');
            }
          }
          
          // Reload page after 1 second to update stats
          setTimeout(() => location.reload(), 1000);
        } else {
          console.error('Update failed:', data.error);
          showToast(data.error || 'Failed to update status', 'danger');
          originalBtn.disabled = false;
          originalBtn.innerHTML = originalText;
        }
      })
      .catch(err => {
        console.error('Fetch error:', err);
        showToast('Network error: ' + err.message, 'danger');
        originalBtn.disabled = false;
        originalBtn.innerHTML = originalText;
      });
    });
  });

  // Auto-refresh every 30 seconds
  setInterval(() => {
    console.log('Auto-refreshing dashboard...');
    location.reload();
  }, 30000);
})();
</script>
