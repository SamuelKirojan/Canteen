<?php
$favorites = $favorites ?? [];
?>

<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3><i class="bi bi-heart-fill text-danger me-2"></i>My Favorites</h3>
      <a href="index.php?r=profile/index" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Profile
      </a>
    </div>

    <?php if (empty($favorites)): ?>
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-heart" style="font-size: 4rem; color: #ddd;"></i>
          <h5 class="mt-3">No favorites yet</h5>
          <p class="text-muted">Start adding your favorite menu items!</p>
          <a href="index.php?r=menu/index" class="btn btn-primary mt-2">
            <i class="bi bi-card-list me-1"></i>Browse Menu
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($favorites as $item): ?>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100">
              <?php if (!empty($item['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>" style="height: 180px; object-fit: cover;">
              <?php endif; ?>
              <div class="card-body">
                <h6 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h6>
                <div class="text-primary fw-semibold mb-2">
                  Rp <?php echo number_format((float)$item['price'] * 1000, 0, ',', '.'); ?>
                </div>
                <?php if (!empty($item['description'])): ?>
                  <p class="small text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                <?php endif; ?>
                <div class="d-flex gap-2 mt-3">
                  <button class="btn btn-sm btn-danger btn-remove-favorite flex-grow-1" data-menu-id="<?php echo $item['id']; ?>">
                    <i class="bi bi-heart-fill me-1"></i>Remove
                  </button>
                  <a href="index.php?r=menu/index" class="btn btn-sm btn-primary">
                    <i class="bi bi-cart-plus"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Toast Container -->
<div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index:1090;">
  <div id="toastContainer"></div>
</div>

<script>
(function(){
  console.log('Favorites page initialized');
  
  function showToast(message, type='success'){
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 show`;
    toast.setAttribute('role', 'alert');
    
    const icon = type === 'success' ? '✓' : '✕';
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <strong style="font-size:1.2rem;margin-right:8px;">${icon}</strong>
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    
    container.appendChild(toast);
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  document.querySelectorAll('.btn-remove-favorite').forEach(btn => {
    btn.addEventListener('click', function(){
      const menuId = this.dataset.menuId;
      const card = this.closest('.col-sm-6, .col-md-4, .col-lg-3');
      
      console.log('Removing favorite:', menuId);
      
      const formData = new FormData();
      formData.append('menu_id', menuId);
      
      fetch('index.php?r=favorite/toggle', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(data => {
        console.log('Response:', data);
        if(data.success){
          showToast('Removed from favorites', 'success');
          card.style.opacity = '0';
          setTimeout(() => {
            card.remove();
            // Check if no favorites left
            if(document.querySelectorAll('.col-sm-6, .col-md-4, .col-lg-3').length === 0){
              location.reload();
            }
          }, 300);
        } else {
          showToast(data.error || 'Failed to remove', 'danger');
        }
      })
      .catch(err => {
        console.error('Error:', err);
        showToast('Network error', 'danger');
      });
    });
  });
})();
</script>
