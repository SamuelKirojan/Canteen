<?php 
$active  = ($tab ?? 'login'); 
$error   = $error ?? '';
$success = $success ?? '';
?>

<section class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background:#f0f2f5;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-7">

        <!-- Top logo / title -->
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-4 mb-3" 
               style="width:64px; height:64px; background:#4169E1; box-shadow:0 4px 12px rgba(65,105,225,.3);">
            <i class="bi bi-heart-pulse-fill text-white" style="font-size: 1.75rem;"></i>
          </div>
          <h4 class="mb-1 fw-bold" style="color:#1a1a1a;">MediCare Portal</h4>
          <div class="text-muted">Doctor Login</div>
        </div>

        <!-- Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
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

            <!-- Tab Content (hidden tabs, visual kept simple) -->
            <div class="tab-content">

              <!-- Sign In Tab -->
              <div id="pane-login" class="tab-pane fade <?php echo $active==='login'?'show active':''; ?>">
                <!-- Header text -->
                <div class="mb-4">
                  <h4 class="fw-bold mb-2" style="color:#1a1a1a;">Welcome Back</h4>
                  <p class="text-muted mb-0">
                    Sign in to access your medical dashboard
                  </p>
                </div>

                <form method="POST" action="index.php?r=auth/login">
                  <div class="mb-3">
                    <label class="form-label fw-semibold mb-2" style="color:#1a1a1a;">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg"
                           placeholder="doctor@hospital.com" required
                           style="background:#f5f5f5; border:1px solid #e0e0e0; border-radius:.5rem; padding:.75rem 1rem;">
                  </div>

                  <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-semibold mb-0" style="color:#1a1a1a;">Password</label>
                      <a href="#" class="text-decoration-none" style="color:#4169E1; font-size:.9rem;">Forgot password?</a>
                    </div>
                    <input type="password" name="password" class="form-control form-control-lg"
                           placeholder="••••••••" required
                           style="background:#f5f5f5; border:1px solid #e0e0e0; border-radius:.5rem; padding:.75rem 1rem;">
                  </div>

                  <button type="submit" 
                          class="btn btn-dark w-100 fw-semibold text-white"
                          style="background:#050414; border:none; border-radius:.5rem; padding:.875rem 0; font-size:1rem; margin-top:1.5rem;">
                    Sign In
                  </button>
                </form>

                <!-- Sign Up Button -->
                <button class="btn btn-outline-secondary w-100 fw-semibold mt-3"
                        data-bs-toggle="tab" data-bs-target="#pane-signup" type="button"
                        style="border:1px solid #d0d0d0; border-radius:.5rem; padding:.875rem 0; font-size:1rem; color:#555;">
                  Sign Up
                </button>

                <!-- Help text -->
                <div class="text-center mt-4">
                  <span class="text-muted">Need help accessing your account?</span>
                  <a href="#" class="text-decoration-none ms-1" style="color:#4169E1; font-weight:500;">Contact IT Support</a>
                </div>
              </div>

              <!-- Sign Up Tab -->
              <div id="pane-signup" class="tab-pane fade <?php echo $active==='signup'?'show active':''; ?>">
                <!-- Header text -->
                <div class="mb-4">
                  <h4 class="fw-bold mb-2" style="color:#1a1a1a;">Create Account</h4>
                  <p class="text-muted mb-0">
                    Sign up to access your medical dashboard
                  </p>
                </div>

                <form method="POST" action="index.php?r=auth/signup">
                  <div class="mb-3">
                    <label class="form-label fw-semibold mb-2" style="color:#1a1a1a;">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg"
                           placeholder="doctor@hospital.com" required
                           style="background:#f5f5f5; border:1px solid #e0e0e0; border-radius:.5rem; padding:.75rem 1rem;">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold mb-2" style="color:#1a1a1a;">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg"
                           placeholder="Minimum 6 characters" required minlength="6"
                           style="background:#f5f5f5; border:1px solid #e0e0e0; border-radius:.5rem; padding:.75rem 1rem;">
                    <small class="text-muted d-block mt-2">Password must be at least 6 characters long.</small>
                  </div>

                  <button type="submit" 
                          class="btn btn-dark w-100 fw-semibold text-white"
                          style="background:#050414; border:none; border-radius:.5rem; padding:.875rem 0; font-size:1rem; margin-top:1.5rem;">
                    Create Account
                  </button>
                </form>

                <!-- Back to Sign In Button -->
                <button class="btn btn-outline-secondary w-100 fw-semibold mt-3"
                        data-bs-toggle="tab" data-bs-target="#pane-login" type="button"
                        style="border:1px solid #d0d0d0; border-radius:.5rem; padding:.875rem 0; font-size:1rem; color:#555;">
                  Back to Sign In
                </button>

                <!-- Help text -->
                <div class="text-center mt-4">
                  <span class="text-muted">Need help accessing your account?</span>
                  <a href="#" class="text-decoration-none ms-1" style="color:#4169E1; font-weight:500;">Contact IT Support</a>
                </div>
              </div>
            </div>

            <!-- Hidden nav tabs for functionality only -->
            <ul class="nav nav-pills d-none" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active==='login'?'active':''; ?>"
                        data-bs-toggle="tab" data-bs-target="#pane-login" type="button" role="tab">
                  Sign In
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active==='signup'?'active':''; ?>"
                        data-bs-toggle="tab" data-bs-target="#pane-signup" type="button" role="tab">
                  Sign Up
                </button>
              </li>
            </ul>

          </div>
        </div>

        <!-- Bottom legal note -->
        <p class="text-center text-muted mt-4 mb-0" style="font-size:.875rem;">
          This portal is for authorized medical staff only. All access is monitored and logged.
        </p>

        <!-- Admin link -->
        <div class="text-center mt-3">
          <span class="text-muted" style="font-size:.875rem;">Are you an admin?</span>
          <a href="index.php?r=admin/login" class="text-decoration-none ms-1" style="color:#4169E1; font-weight:500; font-size:.875rem;">
            Admin Login
          </a>
        </div>

      </div>
    </div>
  </div>
</section>