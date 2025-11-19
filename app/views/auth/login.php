<?php include __DIR__.'/_auth_layout_start.php'; ?>

<div class="login-wrapper">
  <div class="login-container">
    <!-- Header with Logo -->
    <div class="login-header">
      <div class="login-logo">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="40" height="40" rx="8" fill="#3B5BDB"/>
          <path d="M20 12C16.7 12 14 14.7 14 18C14 19.8 14.9 21.4 16.3 22.3C15.1 23.1 14 24.5 14 26.2V27H26V26.2C26 24.5 24.9 23.1 23.7 22.3C25.1 21.4 26 19.8 26 18C26 14.7 23.3 12 20 12ZM20 14C22.2 14 24 15.8 24 18C24 20.2 22.2 22 20 22C17.8 22 16 20.2 16 18C16 15.8 17.8 14 20 14Z" fill="white"/>
        </svg>
      </div>
      <h1 class="login-title">MediCare Portal</h1>
      <p class="login-subtitle">Doctor Login</p>
    </div>

    <!-- Form Card -->
    <div class="login-card">
      <h2 class="form-heading">Welcome Back</h2>
      <p class="form-description">Sign in to access your medical dashboard</p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="post" action="index.php?r=auth/login" novalidate>
        <!-- Email Field -->
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="doctor@hospital.com" required>
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <div class="form-label-row">
            <label class="form-label">Password</label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-signin">Sign In</button>

        <!-- Support Link -->
        <div class="form-footer">
          <span class="support-text">Need help accessing your account? <a href="#" class="support-link">Contact IT Support</a></span>
        </div>
      </form>
    </div>

    <!-- Disclaimer -->
    <p class="login-disclaimer">This portal is for authorized medical staff only. All access is monitored and logged.</p>
  </div>
</div>

<style>
  body {
    background-color: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
  }

  .login-wrapper {
    width: 100%;
    max-width: 550px;
  }

  .login-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 32px;
  }

  .login-header {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    margin-top: 20px;
  }

  .login-logo {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-title {
    font-size: 20px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
  }

  .login-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
  }

  .login-card {
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    width: 100%;
  }

  .form-heading {
    font-size: 22px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 8px 0;
  }

  .form-description {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 24px 0;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .form-label {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0;
    display: block;
  }

  .form-control {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    background-color: #f9fafb;
    transition: all 0.2s;
    box-sizing: border-box;
  }

  .form-control:focus {
    outline: none;
    background-color: white;
    border-color: #3b5bdb;
    box-shadow: 0 0 0 3px rgba(59, 91, 219, 0.1);
  }

  .form-control::placeholder {
    color: #9ca3af;
  }

  .forgot-link {
    font-size: 13px;
    color: #3b5bdb;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
  }

  .forgot-link:hover {
    color: #2e46b8;
  }

  .btn-signin {
    width: 100%;
    padding: 12px 16px;
    background-color: #1a1a1a;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 8px;
  }

  .btn-signin:hover {
    background-color: #2d2d2d;
  }

  .btn-signin:active {
    transform: scale(0.98);
  }

  .form-footer {
    text-align: center;
    margin-top: 20px;
  }

  .support-text {
    font-size: 13px;
    color: #6b7280;
  }

  .support-link {
    color: #3b5bdb;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
  }

  .support-link:hover {
    color: #2e46b8;
  }

  .login-disclaimer {
    font-size: 12px;
    color: #9ca3af;
    text-align: center;
    margin: 0;
  }

  .alert {
    margin-bottom: 16px;
  }
</style>

<?php include __DIR__.'/_auth_layout_end.php'; ?>