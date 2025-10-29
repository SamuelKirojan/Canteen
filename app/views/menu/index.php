<?php
// $items expected: array of [id, name, price, stock, description, image_url]
?>
<section id="menu" class="menu">
  <div class="container" data-aos="fade-up">
    <div class="section-header">
      <h2>Our Menu</h2>
    </div>

    <div class="row gy-5">
      <?php foreach ($items as $item): ?>
        <div class="col-lg-4 menu-item">
          <?php if (!empty($item['image_url'])): ?>
            <?php 
              $img = $item['image_url']; 
              // If it's absolute (http or starts with /), leave it, else keep it relative to public/
              if (!preg_match('#^https?://|^/#', $img)) {
                  $img = ltrim($img, '/');
              }
            ?>
            <a href="<?php echo htmlspecialchars($img); ?>" class="glightbox">
              <img src="<?php echo htmlspecialchars($img); ?>" class="menu-img img-fluid" alt="<?php echo htmlspecialchars($item['name']); ?>">
            </a>
          <?php endif; ?>
          <h4><?php echo htmlspecialchars($item['name']); ?></h4>
          <?php if ($item['price'] !== null): ?>
            <p class="price">Rp <?php echo number_format((float)$item['price'] * 1000, 0, ',', '.'); ?></p>
          <?php endif; ?>
          <?php if ($item['stock'] !== null): ?>
            <p class="ingredients">stock = <?php echo (int)$item['stock']; ?></p>
          <?php endif; ?>
          <?php if (!empty($item['description'])): ?>
            <p><?php echo htmlspecialchars($item['description']); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
