<?php
$error = $error ?? '';
$success = $success ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Signup - Kantin Unklab</title>
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
    .signup-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
      max-width: 450px;
      width: 100%;
    }
    .signup-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      border-radius: 1rem 1rem 0 0;
      text-align: center;
    }
    .signup-body {
      padding: 2rem;
    }
    .btn-signup {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      padding: 0.75rem;
      font-weight: 600;
    }
    .btn-signup:hover {
      background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
  </style>
</head>
<body>
  <div class="signup-card">
    <div class="signup-header">
      <i class="bi bi-person-plus-fill" style="font-size: 3rem;"></i>
      <h3 class="mt-3 mb-0">Admin Signup</h3>
      <p class="mb-0 small">Create Admin Account</p>
    </div>
    <div class="signup-body">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="index.php?r=admin/signup" id="signupForm">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="name" name="name" required autofocus>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required minlength="6">
          </div>
          <small class="text-muted">Minimum 6 characters</small>
        </div>
        
        <div class="mb-4">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-signup w-100">
          <i class="bi bi-person-plus me-2"></i>Create Admin Account
        </button>
      </form>
      
      <div class="text-center mt-3">
        <a href="index.php?r=admin/login" class="text-decoration-none">
          Already have an account? Login
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('signupForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirm = document.getElementById('confirm_password').value;
      
      if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
      }
      
      if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters!');
        return false;
      }
      
      console.log('Admin signup form submitted');
    });
  </script>
</body>
</html>
