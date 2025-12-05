# PHP Version Upgrade Guide for WAMP

## Current Version: PHP 7.4.33
## Target Version: PHP 8.2+

---

## Option 1: Update WAMP (Recommended)

### Step 1: Download PHP 8.2 for WAMP
1. Go to: https://wampserver.aviatechno.net/
2. Download the PHP 8.2 addon for WAMP64
3. Run the installer
4. It will automatically integrate with WAMP

### Step 2: Switch PHP Version
1. Click WAMP icon in system tray (green/orange)
2. Go to: PHP → Version
3. Select PHP 8.2.x
4. WAMP will restart with new version

### Step 3: Verify
```bash
php -v
```
Should show: PHP 8.2.x

---

## Option 2: Manual PHP Installation (Advanced)

### Step 1: Download PHP 8.2
1. Go to: https://windows.php.net/download/
2. Download: PHP 8.2.x (x64 Thread Safe)
3. Extract to: `C:\wamp64\bin\php\php8.2.x`

### Step 2: Copy php.ini
1. Copy `C:\wamp64\bin\php\php7.4.33\php.ini`
2. Paste to: `C:\wamp64\bin\php\php8.2.x\`
3. Edit and verify extensions are correct

### Step 3: Update Apache Config
1. Open: `C:\wamp64\bin\apache\apache2.4.x\conf\httpd.conf`
2. Find: `LoadModule php_module`
3. Update path to: `C:/wamp64/bin/php/php8.2.x/php8apache2_4.dll`

### Step 4: Restart WAMP
- Left-click WAMP icon
- Click "Restart All Services"

---

## Option 3: Use WAMP's Built-in Updater

1. Open WAMP
2. Left-click WAMP icon
3. Click "Check for updates"
4. If PHP 8.2 addon available, install it
5. Switch to new version from PHP menu

---

## After Upgrade

### Update Composer Dependencies
```bash
cd C:\wamp64\www\GHI
composer update
```

### Clear Cache
```bash
# Delete vendor folder and reinstall
rmdir /s /q vendor
composer install
```

### Test Your Site
- Visit: http://localhost/GHI/admin/
- Check for errors
- Run: http://localhost/GHI/scripts/test-database.php

---

## Benefits of PHP 8.2

- **2x faster** performance with JIT compiler
- **OPcache** improvements
- **Better type system**
- **Modern syntax** (match expressions, enums)
- **Security updates**
- **Better error handling**

---

## Troubleshooting

### Issue: WAMP won't start
**Solution**: Check Apache error logs at `C:\wamp64\logs\apache_error.log`

### Issue: Extensions not loading
**Solution**: Edit `php.ini` and enable required extensions:
```ini
extension=mysqli
extension=pdo_mysql
extension=curl
extension=openssl
extension=mbstring
```

### Issue: Composer errors
**Solution**: Update Composer itself:
```bash
composer self-update
composer update
```

---

## Quick Command to Check Version

```bash
php -v
```

You should see something like:
```
PHP 8.2.x (cli) (built: Nov 1 2024 12:34:56) ( ZTS Visual C++ 2019 x64 )
```

