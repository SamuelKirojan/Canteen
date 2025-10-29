<?php
// Database Connection Test
// Upload to: /var/www/html/Canteen/public/test_db.php
// Access: http://YOUR_IP/Canteen/public/test_db.php


require_once __DIR__ . '/../app/config/config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        pre { background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h2>🔍 Database Connection Test</h2>
    <pre><?php

echo "Configuration:\n";
echo "================\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_PASS: " . (DB_PASS ? '***SET***' : '<span class="error">NOT SET</span>') . "\n";
echo "DB_CHARSET: " . DB_CHARSET . "\n\n";

echo "PHP PDO Extensions:\n";
echo "===================\n";
$extensions = get_loaded_extensions();
$pdo_found = false;
$pdo_mysql_found = false;

foreach ($extensions as $ext) {
    if (stripos($ext, 'pdo') !== false) {
        echo "✅ $ext\n";
        if ($ext === 'PDO') $pdo_found = true;
        if ($ext === 'pdo_mysql') $pdo_mysql_found = true;
    }
}

if (!$pdo_found || !$pdo_mysql_found) {
    echo '<span class="error">❌ PDO MySQL extension not found!</span>' . "\n";
    echo "Run: sudo apt-get install php-mysql\n";
}

echo "\nConnection Test:\n";
echo "================\n";

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo '<span class="success">✅ Connection successful!</span>' . "\n\n";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: $version\n\n";
    
    // List tables
    echo "Tables in database:\n";
    echo "-------------------\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo '<span class="error">⚠️  No tables found! Import canteen.sql</span>' . "\n";
    } else {
        foreach ($tables as $table) {
            // Count rows
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "  ✓ $table ($count rows)\n";
        }
    }
    
    echo "\n";
    echo '<span class="success">🎉 Database is working correctly!</span>' . "\n";
    
} catch (PDOException $e) {
    echo '<span class="error">❌ Connection failed!</span>' . "\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "Troubleshooting:\n";
    echo "----------------\n";
    
    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "1. Install PDO MySQL: sudo apt-get install php-mysql\n";
        echo "2. Restart Apache: sudo systemctl restart apache2\n";
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "1. Check DB_PASS in config.php\n";
        echo "2. Verify MySQL user exists\n";
        echo "3. Test: mysql -u " . DB_USER . " -p " . DB_NAME . "\n";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "1. Create database: mysql -u root -p -e \"CREATE DATABASE canteen;\"\n";
        echo "2. Import SQL: mysql -u root -p canteen < canteen.sql\n";
    } else {
        echo "1. Check MySQL is running: sudo systemctl status mysql\n";
        echo "2. Check error log: sudo tail /var/log/apache2/error.log\n";
    }
}

?></pre>

<h3>Next Steps:</h3>
<ul>
    <li>If connection successful, delete this file for security</li>
    <li>Access your app: <a href="index.php?r=home/index">index.php?r=home/index</a></li>
    <li>Admin login: <a href="index.php?r=admin/login">index.php?r=admin/login</a></li>
</ul>

</body>
</html>
