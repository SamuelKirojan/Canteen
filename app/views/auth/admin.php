<?php include __DIR__.'/_auth_layout_start.php'; ?>
  <h4 class="mb-4">Admin Sign In</h4>
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="post" action="index.php?r=auth/admin" novalidate>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" placeholder="Value" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" placeholder="Value" required>
    </div>
    <button type="submit" class="btn btn-dark w-100">Sign In</button>
    <div class="mt-3 small">
      <a href="index.php?r=auth/login">Login as User</a>
    </div>
  </form>
<?php include __DIR__.'/_auth_layout_end.php'; ?>
