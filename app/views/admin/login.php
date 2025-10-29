<?php
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Kantin Unklab</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
      max-width: 400px;
      width: 100%;
    }
    .login-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      border-radius: 1rem 1rem 0 0;
      text-align: center;
    }
    .login-body {
      padding: 2rem;
    }
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      padding: 0.75rem;
      font-weight: 600;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <i class="bi bi-shield-lock" style="font-size: 3rem;"></i>
      <h3 class="mt-3 mb-0">Admin Login</h3>
      <p class="mb-0 small">Kantin Unklab Management</p>
    </div>
    <div class="login-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="index.php?r=admin/login" id="loginForm">
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
          </div>
        </div>
        
        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-login w-100">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
      </form>
      
      <div class="text-center mt-3">
        <a href="index.php?r=admin/signup" class="text-decoration-none">
          Don't have an account? Create Admin Account
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      
      if (!email || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
        return false;
      }
      
      console.log('Admin login attempt:', email);
    });
  </script>
</body>
</html>
