# Asset Path Fix for Server Deployment

## Problem
CSS and JS files were not loading on the server because paths were relative (`assets/...`) instead of absolute.

## Solution
Added dynamic base URL detection in `layout.php` that works for both:
- Local: `http://localhost/Canteen/`
- Server: `http://103.174.115.217/canteen/public/`

## What Was Changed

### 1. Added Base URL Detection (layout.php)
```php
<?php
// Define base URL for assets
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if (basename($baseUrl) === 'public') {
    $baseUrl = dirname($baseUrl);
}
$baseUrl = $baseUrl . '/';
?>
```

### 2. Updated All Asset Paths

**Before:**
```html
<link href="assets/css/main.css" rel="stylesheet">
<script src="assets/js/main.js"></script>
<img src="assets/img/LogoUnklab.png">
```

**After:**
```html
<link href="<?php echo $baseUrl; ?>assets/css/main.css" rel="stylesheet">
<script src="<?php echo $baseUrl; ?>assets/js/main.js"></script>
<img src="<?php echo $baseUrl; ?>assets/img/LogoUnklab.png">
```

## Files Fixed in layout.php

### CSS Files:
- ✅ `assets/img/favicon.png`
- ✅ `assets/img/apple-touch-icon.png`
- ✅ `assets/vendor/bootstrap/css/bootstrap.min.css`
- ✅ `assets/vendor/bootstrap-icons/bootstrap-icons.css`
- ✅ `assets/vendor/aos/aos.css`
- ✅ `assets/vendor/glightbox/css/glightbox.min.css`
- ✅ `assets/vendor/swiper/swiper-bundle.min.css`
- ✅ `assets/css/main.css`

### JavaScript Files:
- ✅ `assets/vendor/bootstrap/js/bootstrap.bundle.min.js`
- ✅ `assets/vendor/aos/aos.js`
- ✅ `assets/vendor/glightbox/js/glightbox.min.js`
- ✅ `assets/vendor/purecounter/purecounter_vanilla.js`
- ✅ `assets/vendor/swiper/swiper-bundle.min.js`
- ✅ `assets/vendor/php-email-form/validate.js`
- ✅ `assets/js/main.js`

### Images:
- ✅ `assets/img/LogoUnklab.png`

## How It Works

### Local (XAMPP):
```
URL: http://localhost/Canteen/public/index.php
$baseUrl = /Canteen/
Result: /Canteen/assets/css/main.css ✅
```

### Server:
```
URL: http://103.174.115.217/canteen/public/index.php
$baseUrl = /canteen/
Result: /canteen/assets/css/main.css ✅
```

## Upload to Server

After making these changes, upload the updated `layout.php`:
```bash
scp app/views/layout.php user@103.174.115.217:/var/www/html/canteen/app/views/
```

Or via FTP/SFTP to:
```
/var/www/html/canteen/app/views/layout.php
```

## Verify

After uploading, refresh the page:
```
http://103.174.115.217/canteen/public/
```

Check browser console - all CSS/JS errors should be gone! ✅

## Additional Notes

If you have other views with asset references (like menu/shop.php, home/landing.php), you may need to:

1. Pass `$baseUrl` to those views
2. Or use the same logic in each view
3. Or create a config file with the base URL

For now, the main layout is fixed and should load all styles correctly!
