<?php 
$active = ($tab ?? 'login'); 
$error = $error ?? '';
$success = $success ?? '';
?>

<section class="py-5" style="min-height: 70vh;">
  <div class="container">
    <div class="row justify-content-center align-items-center g-5">
      <div class="col-lg-5 col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 1rem;">
          <div class="card-body p-4">
            <h3 class="text-center mb-4 fw-bold">User Account</h3>
            
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>
            
            <!-- Tabs -->
            <ul class="nav nav-pills nav-fill mb-4" role="tablist" style="background: #f8f9fa; border-radius: 0.5rem; padding: 0.25rem;">
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active==='login'?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#pane-login" type="button" role="tab" style="border-radius: 0.375rem;">
                  <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active==='signup'?'active':''; ?>" data-bs-toggle="tab" data-bs-target="#pane-signup" type="button" role="tab" style="border-radius: 0.375rem;">
                  <i class="bi bi-person-plus me-1"></i>Sign Up
                </button>
              </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content">
              <!-- Sign In Tab -->
              <div id="pane-login" class="tab-pane fade <?php echo $active==='login'?'show active':''; ?>">
                <form method="POST" action="index.php?r=auth/login">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                      <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                      <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                  </button>
                </form>
              </div>
              
              <!-- Sign Up Tab -->
              <div id="pane-signup" class="tab-pane fade <?php echo $active==='signup'?'show active':''; ?>">
                <form method="POST" action="index.php?r=auth/signup">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                      <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                      <input type="password" name="password" class="form-control" placeholder="Create password (min 6 chars)" required minlength="6">
                    </div>
                    <small class="text-muted">Minimum 6 characters</small>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                  </button>
                </form>
              </div>
            </div>
            
            <!-- Admin Link -->
            <div class="text-center mt-4 pt-3 border-top">
              <p class="text-muted small mb-2">Are you an admin?</p>
              <a href="index.php?r=admin/login" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-shield-lock me-1"></i>Admin Login
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Image Column -->
      <div class="col-lg-5 col-md-6 text-center d-none d-md-block">
        <img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg" class="img-fluid" alt="Chef" style="max-height: 500px; object-fit: cover;">
      </div>
    </div>
  </div>
</section>
