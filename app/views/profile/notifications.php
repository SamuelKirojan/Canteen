<?php
$notifications = $notifications ?? [];
?>

<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3><i class="bi bi-bell-fill text-primary me-2"></i>Notifications</h3>
      <div>
        <?php if (!empty($notifications)): ?>
          <button id="btnMarkAllRead" class="btn btn-outline-primary btn-sm me-2">
            <i class="bi bi-check-all me-1"></i>Mark All Read
          </button>
        <?php endif; ?>
        <a href="index.php?r=profile/index" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Back
        </a>
      </div>
    </div>

    <?php if (empty($notifications)): ?>
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-bell-slash" style="font-size: 4rem; color: #ddd;"></i>
          <h5 class="mt-3">No notifications</h5>
          <p class="text-muted">You're all caught up!</p>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="list-group list-group-flush">
          <?php foreach ($notifications as $notif): ?>
            <?php
              $typeIcons = [
                'success' => 'check-circle-fill text-success',
                'info' => 'info-circle-fill text-info',
                'warning' => 'exclamation-triangle-fill text-warning',
                'error' => 'x-circle-fill text-danger'
              ];
              $icon = $typeIcons[$notif['type']] ?? 'bell-fill text-primary';
              $isRead = (int)$notif['is_read'] === 1;
            ?>
            <div class="list-group-item <?php echo $isRead ? '' : 'bg-light'; ?>" data-notif-id="<?php echo $notif['id']; ?>">
              <div class="d-flex">
                <div class="me-3">
                  <i class="bi bi-<?php echo $icon; ?>" style="font-size: 1.5rem;"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-1 <?php echo $isRead ? 'text-muted' : ''; ?>">
                      <?php echo htmlspecialchars($notif['title']); ?>
                      <?php if (!$isRead): ?>
                        <span class="badge bg-primary rounded-pill ms-2">New</span>
                      <?php endif; ?>
                    </h6>
                    <small class="text-muted"><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></small>
                  </div>
                  <p class="mb-1 <?php echo $isRead ? 'text-muted' : ''; ?>">
                    <?php echo htmlspecialchars($notif['message']); ?>
                  </p>
                  <?php if ($notif['order_id']): ?>
                    <a href="index.php?r=order/info&id=<?php echo $notif['order_id']; ?>" class="btn btn-sm btn-outline-primary mt-2">
                      <i class="bi bi-eye me-1"></i>View Order
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
(function(){
  console.log('Notifications page initialized');
  
  const btnMarkAllRead = document.getElementById('btnMarkAllRead');
  if(btnMarkAllRead){
    btnMarkAllRead.addEventListener('click', function(){
      console.log('Marking all as read');
      // In a real implementation, you'd call an API here
      // For now, just reload the page
      alert('Feature coming soon: Mark all as read');
    });
  }
})();
</script>
