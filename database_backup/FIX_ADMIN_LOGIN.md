# Admin Login Fix - Step by Step

## Issues Fixed

1. ✅ **Layout showing on login page** - Fixed by adding `hideChrome` support in Controller
2. ✅ **Better error logging** - Added detailed logs for debugging
3. ✅ **Password verification** - Ensured correct password hash is used

## Step 1: Test Admin Account

Open this URL in your browser:
```
http://localhost/Canteen/test_admin.php
```

This will show you:
- ✅ If admin account exists
- ✅ If password hash is correct
- ✅ If password verification works

## Step 2: Fix Based on Test Results

### If admin account NOT found:
Run this SQL in phpMyAdmin:
```sql
INSERT INTO admins (email, password, name, created_at) 
VALUES ('admin@canteen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NOW());
```

### If password verification FAILED:
Run this SQL in phpMyAdmin:
```sql
UPDATE admins 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@canteen.com';
```

## Step 3: Try Login Again

1. Go to: `http://localhost/Canteen/index.php?r=admin/login`
2. Enter:
   - Email: `admin@canteen.com`
   - Password: `admin123`
3. Click Login

## Step 4: Check Error Logs (if still failing)

Open: `C:\xampp\apache\logs\error.log`

Look for lines like:
```
Admin login: No admin found with email: admin@canteen.com
Admin login: Password verification failed for: admin@canteen.com
Admin login error: [error message]
```

## Alternative: Create New Admin with Different Password

If you want to use a different password, run this PHP script:

```php
<?php
// Generate new password hash
$newPassword = 'YourNewPassword123';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
echo "New hash: " . $hash;
?>
```

Then update the database:
```sql
UPDATE admins 
SET password = 'paste_the_hash_here' 
WHERE email = 'admin@canteen.com';
```

## Troubleshooting

### Login page shows footer/header
- ✅ FIXED: Controller now checks `hideChrome` flag

### "Invalid email or password" error
- Check `test_admin.php` to verify account exists
- Check password hash matches
- Check error logs for details

### Database connection error
- Verify MySQL is running in XAMPP
- Check database name is 'canteen'
- Check Database.php has correct credentials

### Session issues
- Clear browser cookies
- Try incognito/private mode
- Check if session_start() is called

## Quick SQL Check

Run this in phpMyAdmin SQL tab:
```sql
-- Check if admin exists
SELECT * FROM admins WHERE email = 'admin@canteen.com';

-- If no results, insert admin
INSERT INTO admins (email, password, name, created_at) 
VALUES ('admin@canteen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', NOW())
ON DUPLICATE KEY UPDATE email=email;
```

## After Successful Login

You should see:
- Admin Dashboard with order queue
- Statistics cards (Not Ready, Ready, Packing, Completed)
- "Manage Menu" button
- "Logout" button

## Need More Help?

1. Run `test_admin.php` and send me the output
2. Check `C:\xampp\apache\logs\error.log` for errors
3. Check browser console (F12) for JavaScript errors
4. Try clearing browser cache and cookies
