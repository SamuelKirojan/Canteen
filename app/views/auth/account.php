<?php 
$active  = ($tab ?? 'login'); 
$error   = $error ?? '';
$success = $success ?? '';
?>

<section class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background:#f5f7fb;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-7">

        <!-- Top logo / title -->
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-4 mb-2" 
               style="width:56px; height:56px; background:#2f5bff; box-shadow:0 8px 20px rgba(47,91,255,.25);">
            <i class="bi bi-bag-check-fill text-white fs-4"></i>
          </div>
          <h5 class="mb-0 fw-semibold">Canteen Portal</h5>
          <div class="text-muted small">User Login</div>
        </div>

        <!-- Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 1.25rem;">
          <div class="card-body p-4 p-md-5">

            <!-- Alerts -->
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
              <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <!-- Header text -->
            <div class="mb-4">
              <h4 class="fw-semibold mb-1">Welcome Back</h4>
              <p class="text-muted mb-0 small">
                Sign in to access your canteen dashboard.
              </p>
            </div>

            <!-- Subtle Sign In / Nurse Login switch (tabs) -->
            <div class="d-flex justify-content-between align-items-center mb-3 small">
              <div class="text-muted">
                <?php if ($active === 'login'): ?>
                  Nurse account?
                  <button class="btn btn-link p-0 align-baseline small" 
                          data-bs-toggle="tab" data-bs-target="#pane-nurse" type="button">
                    Click here
                  </button>
                <?php else: ?>
                  Doctor account?
                  <button class="btn btn-link p-0 align-baseline small" 
                          data-bs-toggle="tab" data-bs-target="#pane-login" type="button">
                    Click here
                  </button>
                <?php endif; ?>
              </div>

              <!-- Actual Bootstrap tabs (kept for functionality) -->
              <ul class="nav nav-pills gap-1" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link py-1 px-2 <?php echo $active==='login'?'active':''; ?> small"
                          data-bs-toggle="tab" data-bs-target="#pane-login" type="button" role="tab">
                    Doctor
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link py-1 px-2 <?php echo $active==='nurse'?'active':''; ?> small"
                          data-bs-toggle="tab" data-bs-target="#pane-nurse" type="button" role="tab">
                    Nurse
                  </button>
                </li>
              </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">

              <!-- Doctor Login Tab -->
              <div id="pane-login" class="tab-pane fade <?php echo $active==='login'?'show active':''; ?>">
                <form method="POST" action="index.php?r=auth/login">
                  <div class="mb-3">
                    <label class="form-label fw-semibold small mb-1">Email</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="user@example.com" required
                           style="background:#f5f5f7; border-radius:.6rem; border-color:#e5e7f1;">
                  </div>

                  <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="form-label fw-semibold small mb-0">Password</label>
                      <a href="#" class="small text-decoration-none" style="color:#2f5bff;">Forgot password?</a>
                    </div>
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter your password" required
                           style="background:#f5f5f7; border-radius:.6rem; border-color:#e5e7f1;">
                  </div>

                  <button type="submit" 
                          class="btn w-100 mt-3 fw-semibold"
                          style="background:#050414; border-color:#050414; border-radius:.8rem; padding:.6rem 0;">
                    Sign In
                  </button>
                </form>
              </div>

              <!-- Nurse Login Tab -->
              <div id="pane-nurse" class="tab-pane fade <?php echo $active==='nurse'?'show active':''; ?>">
                <form method="POST" action="index.php?r=admin/login">
                  <div class="mb-3">
                    <label class="form-label fw-semibold small mb-1">Email</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="nurse@example.com" required
                           style="background:#f5f5f7; border-radius:.6rem; border-color:#e5e7f1;">
                  </div>

                  <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label class="form-label fw-semibold small mb-0">Password</label>
                      <a href="#" class="small text-decoration-none" style="color:#2f5bff;">Forgot password?</a>
                    </div>
                    <input type="password" name="password" class="form-control"
                           placeholder="Enter your password" required
                           style="background:#f5f5f7; border-radius:.6rem; border-color:#e5e7f1;">
                  </div>

                  <button type="submit" 
                          class="btn w-100 mt-3 fw-semibold"
                          style="background:#050414; border-color:#050414; border-radius:.8rem; padding:.6rem 0;">
                    Sign In
                  </button>
                </form>
              </div>
            </div>

            <!-- Divider -->
            <hr class="my-4">

            <!-- Help / IT support text -->
            <div class="text-center small mb-1">
              Need help accessing your account?
              <a href="#" class="text-decoration-none" style="color:#2f5bff;">Contact Support</a>
            </div>

          </div>
        </div>

        <!-- Bottom legal note -->
        <p class="text-center text-muted small mt-3 mb-0">
          This portal is for authorized users only. All access is monitored and logged.
        </p>

      </div>
    </div>
  </div>
</section>