# Server Deployment Guide

## 🚀 Deploy to Ubuntu Server (103.174.115.217)

### Current Issue:
- URL: `http://103.174.115.217/index.php?r=home/index`
- Error: **404 Not Found**
- Server: Apache/2.4.58 (Ubuntu)

---

## 📁 Step 1: Upload Files to Server

Upload your Canteen folder to:
```bash
/var/www/html/canteen/
```

Your structure should be:
```
/var/www/html/canteen/
├── app/
├── public/
│   ├── index.php
│   └── assets/
├── assets/
└── database_backup/
```

---

## 🔧 Step 2: Configure .htaccess Files

### A. Root .htaccess
Create `/var/www/html/canteen/.htaccess`:
```bash
cd /var/www/html/canteen/
nano .htaccess
```

Copy contents from `SERVER_HTACCESS_ROOT.txt`

### B. Public .htaccess
Create `/var/www/html/canteen/public/.htaccess`:
```bash
cd /var/www/html/canteen/public/
nano .htaccess
```

Copy contents from `SERVER_HTACCESS_PUBLIC.txt`

### C. App .htaccess (Already exists)
Keep the existing `/var/www/html/canteen/app/.htaccess`

---

## 🔑 Step 3: Set Permissions

```bash
# Navigate to canteen directory
cd /var/www/html/canteen/

# Set ownership to www-data (Apache user)
sudo chown -R www-data:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Make sure .htaccess files are readable
sudo chmod 644 .htaccess
sudo chmod 644 public/.htaccess
sudo chmod 644 app/.htaccess
```

---

## ⚙️ Step 4: Enable Apache Modules

```bash
# Enable mod_rewrite (required for .htaccess)
sudo a2enmod rewrite

# Enable mod_headers (for security headers)
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2
```

---

## 📝 Step 5: Configure Apache Virtual Host

Edit Apache configuration:
```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

Add or modify:
```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/canteen/public
    
    <Directory /var/www/html/canteen/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    <Directory /var/www/html/canteen/app>
        Require all denied
    </Directory>
    
    <Directory /var/www/html/canteen/database_backup>
        Require all denied
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Restart Apache:
```bash
sudo systemctl restart apache2
```

---

## 🗄️ Step 6: Configure Database

### A. Import Database
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE canteen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (if needed)
CREATE USER 'canteen_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON canteen.* TO 'canteen_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database
mysql -u root -p canteen < /var/www/html/canteen/database_backup/canteen.sql
```

### B. Update Database Config
Edit `/var/www/html/canteen/app/config/config.php`:
```php
<?php
define('APP_ROOT', '/var/www/html/canteen');

define('DB_HOST', 'localhost');
define('DB_NAME', 'canteen');
define('DB_USER', 'canteen_user');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');
```

---

## 🌐 Step 7: Access URLs

After configuration, access your site:

### Option 1: With /canteen/ path
```
http://103.174.115.217/canteen/
http://103.174.115.217/canteen/public/index.php?r=home/index
```

### Option 2: Root domain (if DocumentRoot is set to public/)
```
http://103.174.115.217/
http://103.174.115.217/index.php?r=home/index
```

---

## 🔍 Troubleshooting

### Issue 1: 404 Not Found
**Check:**
```bash
# Verify files exist
ls -la /var/www/html/canteen/public/index.php

# Check Apache error log
sudo tail -f /var/log/apache2/error.log

# Verify mod_rewrite is enabled
apache2ctl -M | grep rewrite
```

### Issue 2: 500 Internal Server Error
**Check:**
```bash
# Check .htaccess syntax
apachectl configtest

# View error log
sudo tail -f /var/log/apache2/error.log

# Check file permissions
ls -la /var/www/html/canteen/
```

### Issue 3: Assets not loading
**Check:**
```bash
# Verify assets directory
ls -la /var/www/html/canteen/public/assets/

# Check permissions
sudo chmod -R 755 /var/www/html/canteen/public/assets/
```

### Issue 4: Database connection error
**Check:**
```bash
# Test MySQL connection
mysql -u canteen_user -p canteen

# Verify config.php
cat /var/www/html/canteen/app/config/config.php
```

---

## 📋 Quick Commands Checklist

```bash
# 1. Upload files to server
scp -r Canteen/ user@103.174.115.217:/var/www/html/

# 2. Set permissions
cd /var/www/html/canteen/
sudo chown -R www-data:www-data .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;

# 3. Enable Apache modules
sudo a2enmod rewrite headers
sudo systemctl restart apache2

# 4. Check Apache status
sudo systemctl status apache2

# 5. View logs
sudo tail -f /var/log/apache2/error.log
```

---

## ✅ Verification

After setup, test these URLs:

**Should Work:**
- `http://103.174.115.217/canteen/`
- `http://103.174.115.217/canteen/public/index.php?r=home/index`
- `http://103.174.115.217/canteen/public/assets/img/chef.jpg`

**Should Fail (403 Forbidden):**
- `http://103.174.115.217/canteen/app/controllers/AuthController.php`
- `http://103.174.115.217/canteen/database_backup/`

---

## 🎯 Expected Result

After following all steps:
```
http://103.174.115.217/canteen/
→ Shows your Canteen homepage ✅
```

---

## 📞 Need Help?

Check logs:
```bash
# Apache error log
sudo tail -50 /var/log/apache2/error.log

# Apache access log
sudo tail -50 /var/log/apache2/access.log

# PHP error log (if configured)
sudo tail -50 /var/log/php_errors.log
```
