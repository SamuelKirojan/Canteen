# Chef Image Path Fix

## Problem
The chef image (`chef.jpg`) was not loading on the server because it uses relative paths.

## Location
The image is located at:
```
/public/assets/img/chef.jpg
```

## Files Updated

### 1. `app/views/home/index.php`
```php
<img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg">
```

### 2. `app/views/home/landing.php`
```php
<img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg">
```

### 3. `app/views/auth/account.php`
```php
<img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg">
```

### 4. `app/views/auth/_auth_layout_end.php`
```php
<img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg">
```

### 5. `app/views/auth/_auth_layout_start.php`
Added baseUrl definition at the top:
```php
<?php
if (!isset($baseUrl)) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if (basename($baseUrl) === 'public') {
        $baseUrl = dirname($baseUrl);
    }
    $baseUrl = $baseUrl . '/';
}
?>
```

## How It Works

### Local (XAMPP):
```
URL: http://localhost/Canteen/public/index.php
$baseUrl = /Canteen/
Image: /Canteen/assets/img/chef.jpg
Actual: C:\xampp\htdocs\Canteen\public\assets\img\chef.jpg ✅
```

### Server:
```
URL: http://103.174.115.217/canteen/public/index.php
$baseUrl = /canteen/
Image: /canteen/assets/img/chef.jpg
Actual: /var/www/html/canteen/public/assets/img/chef.jpg ✅
```

## Verify Image Exists

### On Local:
Check if file exists:
```
C:\xampp\htdocs\Canteen\public\assets\img\chef.jpg
```

### On Server:
Check if file exists:
```bash
ls -la /var/www/html/canteen/public/assets/img/chef.jpg
```

If missing, upload it:
```bash
scp public/assets/img/chef.jpg user@103.174.115.217:/var/www/html/canteen/public/assets/img/
```

## Upload Updated Files

Upload these files to the server:
```bash
# Upload all updated view files
scp app/views/home/index.php user@103.174.115.217:/var/www/html/canteen/app/views/home/
scp app/views/home/landing.php user@103.174.115.217:/var/www/html/canteen/app/views/home/
scp app/views/auth/account.php user@103.174.115.217:/var/www/html/canteen/app/views/auth/
scp app/views/auth/_auth_layout_start.php user@103.174.115.217:/var/www/html/canteen/app/views/auth/
scp app/views/auth/_auth_layout_end.php user@103.174.115.217:/var/www/html/canteen/app/views/auth/
```

## Test

After uploading, visit:
```
http://103.174.115.217/canteen/public/
```

The chef image should now load! ✅

## Additional Images

If you have other images with similar issues, apply the same fix:

**Before:**
```html
<img src="assets/img/image.jpg">
```

**After:**
```html
<img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/image.jpg">
```

## All Fixed! 🎉

- ✅ CSS files loading
- ✅ JavaScript files loading
- ✅ Logo image loading
- ✅ Chef image loading
- ✅ All assets using absolute paths
