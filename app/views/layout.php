<?php
// Define base URL for assets
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim($scriptDir, '/');

// If we're in /public/, go up one level for base, but assets are in public
if (basename($baseUrl) === 'public') {
    $baseUrl = dirname($baseUrl) . '/public/';
} else {
    // If not in public subdirectory, assets should be relative to current
    $baseUrl = $baseUrl . '/';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Kantin Unklab</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="<?php echo $baseUrl; ?>assets/img/favicon.png" rel="icon">
  <link href="<?php echo $baseUrl; ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&family=Amatic+SC:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="<?php echo $baseUrl; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $baseUrl; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $baseUrl; ?>assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo $baseUrl; ?>assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo $baseUrl; ?>assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="<?php echo $baseUrl; ?>assets/css/main.css" rel="stylesheet">
</head>
<body>
  <?php if (empty($hideChrome)): ?>
    <header id="header" class="header fixed-top d-flex align-items-center">
      <div class="container d-flex align-items-center justify-content-between">
        <a href="index.php?r=home/index" class="logo d-flex align-items-center me-auto me-lg-0">
          <h1>Samuel</h1>
        </a>
        <nav id="navbar" class="navbar">
          <ul>
            <?php if (empty($hideLandingLinks)): ?>
              <li><a href="index.php?r=home/index#hero">Home</a></li>
              <li><a href="index.php?r=home/index#contact">Contact</a></li>
            <?php endif; ?>
            <?php if (!empty($_SESSION['user_email']) || !empty($_SESSION['admin_email'])): ?>
              <?php if (empty($hideAppLinks)): ?>
                <li><a href="index.php?r=menu/index">Menu</a></li>
                <li><a href="index.php?r=order/history">History</a></li>
                <?php if (!empty($_SESSION['user_id'])): ?>
                  <li><a href="index.php?r=profile/index">Profile</a></li>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (!empty($_SESSION['admin_id'])): ?>
                <li><a href="index.php?r=admin/dashboard">Admin Dashboard</a></li>
              <?php endif; ?>
              <li><a href="index.php?r=auth/logout">Logout</a></li>
            <?php else: ?>
              <li><a target="_blank" href="index.php?r=auth/account">Account</a></li>
            <?php endif; ?>
          </ul>
        </nav>
        <img src="<?php echo $baseUrl; ?>assets/img/LogoUnklab.png" alt="LogoUnklab" style="width: 250px;">
        <i class="mobile-nav-toggle mobile-nav-show bi bi-list"></i>
        <i class="mobile-nav-toggle mobile-nav-hide d-none bi bi-x"></i>
      </div>
    </header>
  <?php elseif (!empty($brandOnly)): ?>
    <header class="py-3 border-bottom" style="position:sticky;top:0;background:#fff;z-index:1080;">
      <div class="container d-flex align-items-center">
        <a href="index.php?r=home/index" class="text-decoration-none fw-bold" style="font-size:1.25rem;">Kantin Online</a>
      </div>
    </header>
  <?php endif; ?>

  <main id="main" style="margin-top: <?php echo empty($hideChrome)?'80px':'0'; ?>;">
    <?php echo $content; ?>
  </main>

  <?php if (empty($hideChrome) && empty($hideFooter)): ?>
    <footer id="footer" class="footer">
      <div class="container">
        <div class="row gy-3">
          <div class="col-lg-3 col-md-6 d-flex">
            <i class="bi bi-geo-alt icon"></i>
            <div>
              <h4>Address</h4>
              <p>Jl. Arnold Mononutu, Airmadidi, Minahasa Utara,<br>Sulawesi Utara, 95371</p>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 footer-links d-flex">
            <i class="bi bi-telephone icon"></i>
            <div>
              <h4>Reservations</h4>
              <p><strong>Phone:</strong> +62431 891035<br><strong>Email:</strong> info@unklab.ac.id</p>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 footer-links d-flex">
            <i class="bi bi-clock icon"></i>
            <div>
              <h4>Opening Hours</h4>
              <p><strong>Mon-Fri: 07AM</strong> - 18PM<br>Saturday-Sunday: Closed</p>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Follow Us</h4>
            <div class="social-links d-flex">
              <a href="https://www.facebook.com/p/UNKLAB-Official-100082571173139/" class="facebook"><i class="bi bi-facebook"></i></a>
              <a href="https://www.instagram.com/unklabofficial/" class="instagram"><i class="bi bi-instagram"></i></a>
            </div>
          </div>
        </div>
      </div>
      <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
      <div id="preloader"></div>
    </footer>
  <?php endif; ?>
  <!-- scripts always included -->
  <script src="<?php echo $baseUrl; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/vendor/aos/aos.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/vendor/php-email-form/validate.js"></script>
  <script src="<?php echo $baseUrl; ?>assets/js/main.js"></script>
</body>
</html>
