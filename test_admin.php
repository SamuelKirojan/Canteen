<?php
// Test script to verify admin account
define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/app/config/config.php';
require_once APP_ROOT . '/app/core/Database.php';

echo "<h2>Admin Account Test</h2>";

try {
    $pdo = Database::getInstance();
    
    // Check if admin exists
    $stmt = $pdo->prepare('SELECT id, email, password, name, created_at FROM admins WHERE email = ?');
    $stmt->execute(['admin@canteen.com']);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo "<p style='color:red;'>❌ Admin account NOT found!</p>";
        echo "<p>Run this SQL to create it:</p>";
        echo "<pre>INSERT INTO admins (email, password, name, created_at) 
VALUES ('admin@canteen.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NOW());</pre>";
    } else {
        echo "<p style='color:green;'>✅ Admin account found!</p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><td>" . $admin['id'] . "</td></tr>";
        echo "<tr><th>Email</th><td>" . $admin['email'] . "</td></tr>";
        echo "<tr><th>Name</th><td>" . $admin['name'] . "</td></tr>";
        echo "<tr><th>Created</th><td>" . $admin['created_at'] . "</td></tr>";
        echo "<tr><th>Password Hash</th><td style='font-size:10px;'>" . $admin['password'] . "</td></tr>";
        echo "</table>";
        
        // Test password verification
        $testPassword = 'admin123';
        $verified = password_verify($testPassword, $admin['password']);
        
        echo "<h3>Password Test</h3>";
        echo "<p>Testing password: <strong>admin123</strong></p>";
        if ($verified) {
            echo "<p style='color:green;'>✅ Password verification SUCCESS!</p>";
            echo "<p>You can login with: admin@canteen.com / admin123</p>";
        } else {
            echo "<p style='color:red;'>❌ Password verification FAILED!</p>";
            echo "<p>The password hash in database doesn't match 'admin123'</p>";
            echo "<p>Run this SQL to fix it:</p>";
            echo "<pre>UPDATE admins SET password = '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE email = 'admin@canteen.com';</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php?r=admin/login'>Go to Admin Login</a></p>";
?>
