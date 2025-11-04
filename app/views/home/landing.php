<?php
// Landing page: Home + Contact only
?>

<!-- ======= Hero Section ======= -->
<section id="hero" class="hero d-flex align-items-center section-bg">
  <div class="container">
    <div class="row justify-content-between gy-5">
      <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center align-items-center align-items-lg-start text-center text-lg-start">
        <h2 data-aos="fade-up">Sehat Lezattttt<br>Unklab Canteen</h2>
        <p data-aos="fade-up" data-aos-delay="100">Kami menyediakan Makanan Pembuka, Makan Siang, Minuman dan Snacks, menemani hari anda dalam beraktivitas di Universitas Klabat.</p>
        <div data-aos="fade-up" data-aos-delay="150">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="index.php?r=menu/index" class="btn btn-primary btn-lg mt-3">Order Now</a>
          <?php else: ?>
            <a href="index.php?r=auth/account" class="btn btn-primary btn-lg mt-3">Order Now</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-5 order-1 order-lg-2 text-center text-lg-start">
        <img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg" class="img-fluid" alt="" data-aos="zoom-out" data-aos-delay="300">
      </div>
    </div>
  </div>
</section><!-- End Hero Section -->

<!-- ======= Contact Section ======= -->
<section id="contact" class="contact">
  <div class="container" data-aos="fade-up">

    <div class="section-header d-flex justify-content-between align-items-center">
      <div>
        <h2>Contact</h2>
        <p>Need Help? <span>Contact Us</span></p>
      </div>
    </div>

    <div class="mb-3">
      <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d997.149158123655!2d124.98266649999998!3d1.4179218!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32870fb08562b233%3A0x39f6f555cdb6eeab!2sStudent%20Canteen%20Universitas%20Klabat!5e0!3m2!1sid!2sid!4v1698071157842!5m2!1sid!2sid" frameborder="0" allowfullscreen></iframe>
    </div><!-- End Google Maps -->

    <div class="row gy-4">

      <div class="col-md-6">
        <div class="info-item  d-flex align-items-center">
          <i class="icon bi bi-map flex-shrink-0"></i>
          <div>
            <h3>Our Address</h3>
            <p>Student Canteen Unklab, depan Asrama Jasmine, Universitas Klabat.</p>
          </div>
        </div>
      </div><!-- End Info Item -->

      <div class="col-md-6">
        <div class="info-item d-flex align-items-center">
          <i class="icon bi bi-envelope flex-shrink-0"></i>
          <div>
            <h3>Email Us</h3>
            <p>info@unklab.ac.id</p>
          </div>
        </div>
      </div><!-- End Info Item -->

      <div class="col-md-6">
        <div class="info-item  d-flex align-items-center">
          <i class="icon bi bi-telephone flex-shrink-0"></i>
          <div>
            <h3>Call Us</h3>
            <p>+62431 891036</p>
          </div>
        </div>
      </div><!-- End Info Item -->

      <div class="col-md-6">
        <div class="info-item  d-flex align-items-center">
          <i class="icon bi bi-share flex-shrink-0"></i>
          <div>
            <h3>Opening Hours</h3>
            <div><strong>Mon-Fri</strong> 07AM - 18PM;
              <strong>Saturday-Sunday:</strong> Closed
            </div>
          </div>
        </div>
      </div><!-- End Info Item -->

    </div>    
  </div>
</section><!-- End Contact Section -->
