# 🚀 Deployment Guide - Global Harmony Initiative

## Production Server Details

**Domain**: globalharmonyinitiative.com  
**Server IP**: 54.37.142.31  
**Home Directory**: /home/jhoffkau  
**Public HTML**: /home/jhoffkau/public_html  

---

## 📋 Pre-Deployment Checklist

### ✅ Local Testing
- [ ] All pages load without errors
- [ ] Database connections work
- [ ] Forms submit correctly
- [ ] Email sending works (test mode)
- [ ] Admin dashboard accessible
- [ ] No PHP errors in logs
- [ ] JavaScript compiled successfully
- [ ] Images load properly

### ✅ Code Preparation
- [ ] Run `composer install --no-dev` (production dependencies)
- [ ] Run `npm run build` (compile assets)
- [ ] Remove development files (.git, node_modules, etc.)
- [ ] Verify `.htaccess.production` is ready
- [ ] Check all environment-specific configs

---

## 🔧 Step-by-Step Deployment

### Step 1: Connect via FTP

**FTP Details**:
```
Host: ftp.globalharmonyinitiative.com
Username: admin@globalharmonyinitiative.com
Password: GHI@admin2025
Port: 21
Protocol: FTP or Explicit FTPS
```

**Recommended FTP Clients**:
- FileZilla (Free, cross-platform)
- Cyber Duck (Mac)
- WinSCP (Windows)

### Step 2: Backup Existing Site (If Any)

```bash
# On production server (via SSH if available)
cd /home/jhoffkau
tar -czf backup-$(date +%Y%m%d).tar.gz public_html/
```

Or via FTP: Download entire `public_html` folder before uploading.

### Step 3: Upload Files

Upload these directories/files to `/home/jhoffkau/public_html/`:

**Essential Directories**:
```
/admin/
/assets/
/config/
/dist/
/includes/
/lib/
/Logo/
/models/
/src/
/uploads/
/vendor/
```

**Essential Files**:
```
index.php
404.php
coming-soon-donate.php
coming-soon-get-involved.php
about.php
causes.php
initiatives.php
events.php
impact.php
stories.php
contact.php
.htaccess (rename from .htaccess.production)
composer.json
```

**Do NOT Upload** (excluded):
```
.git/
.gitignore
node_modules/
.env.example
.htaccess.development
*.md (documentation files)
tests/
.vscode/
```

### Step 4: Configure .htaccess

1. Delete the uploaded `.htaccess.production` file name
2. Rename `.htaccess.production` to `.htaccess`
3. Verify permissions: 644

```bash
# Via SSH
cd /home/jhoffkau/public_html
mv .htaccess.production .htaccess
chmod 644 .htaccess
```

### Step 5: Set File Permissions

**Recommended Permissions**:
```bash
# Directories
find /home/jhoffkau/public_html -type d -exec chmod 755 {} \;

# PHP Files
find /home/jhoffkau/public_html -type f -name "*.php" -exec chmod 644 {} \;

# Writable directories
chmod 775 /home/jhoffkau/public_html/uploads
chmod 775 /home/jhoffkau/public_html/uploads/images
chmod 775 /home/jhoffkau/public_html/cache

# Config files (more restrictive)
chmod 640 /home/jhoffkau/public_html/config/*.php
```

### Step 6: Create Database

**Via cPanel**:
1. Log into cPanel
2. Go to MySQL® Databases
3. Create database: `jhoffkau_GHI`
4. Create user: `jhoffkau_admin`
5. Set password: `GHI@admin2025`
6. Grant ALL PRIVILEGES to user on database

**Database Configuration** (already in `config/environment.php`):
```php
'db_host' => 'localhost',
'db_name' => 'jhoffkau_GHI',
'db_user' => 'jhoffkau_admin',
'db_pass' => 'GHI@admin2025',
```

### Step 7: Import Database

**Option A: via phpMyAdmin**
1. Open phpMyAdmin in cPanel
2. Select `jhoffkau_GHI` database
3. Click "Import"
4. Upload your SQL file
5. Click "Go"

**Option B: via SSH**
```bash
cd /home/jhoffkau/public_html
mysql -u jhoffkau_admin -p'GHI@admin2025' jhoffkau_GHI < database_export.sql
```

### Step 8: Verify Environment Detection

Visit your site: https://www.globalharmonyinitiative.com

The system should automatically detect production environment based on:
- Domain name contains "globalharmonyinitiative.com"
- Server IP is 54.37.142.31
- Document root contains "/home/jhoffkau"

**Test URL**: https://www.globalharmonyinitiative.com/test-environment.php

Create this file temporarily:
```php
<?php
require_once __DIR__ . '/config/config.php';
echo 'Environment: ' . ENVIRONMENT . '<br>';
echo 'Database: ' . DB_NAME . '<br>';
echo 'Base URL: ' . BASE_URL . '<br>';
echo 'Mail Host: ' . get_environment_config()['mail_host'] . '<br>';
// DELETE THIS FILE AFTER TESTING!
```

### Step 9: Test Email Configuration

**Test via Contact Form**:
1. Go to https://www.globalharmonyinitiative.com/contact
2. Fill out and submit
3. Check if email arrives at admin@globalharmonyinitiative.com

**SMTP Settings** (configured in `config/environment.php`):
```php
'mail_host' => 'mail.globalharmonyinitiative.com',
'mail_port' => 465,
'mail_encryption' => 'ssl',
'mail_username' => 'admin@globalharmonyinitiative.com',
'mail_password' => 'GHI@admin2025',
```

**Troubleshooting Email**:
- Check `/home/jhoffkau/logs/php_error.log` for SMTP errors
- Verify email account exists in cPanel
- Test SMTP settings with online tools
- Enable less secure apps if needed

### Step 10: Configure SSL/HTTPS

**Via cPanel (AutoSSL)**:
1. Go to cPanel → SSL/TLS Status
2. Enable AutoSSL for your domain
3. Wait for certificate generation (15 minutes)
4. Visit https://www.globalharmonyinitiative.com
5. Verify green padlock appears

**Force HTTPS** is already configured in `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

### Step 11: Clear Caches

```bash
# Via SSH
cd /home/jhoffkau/public_html
rm -rf cache/*
rm -rf uploads/cache/*
```

Or via FTP: Delete contents of `cache/` folder

### Step 12: Test All Functionality

**Critical Tests**:
- [ ] Homepage loads: https://www.globalharmonyinitiative.com
- [ ] Admin login: https://www.globalharmonyinitiative.com/admin
- [ ] Database queries work (causes, initiatives, etc.)
- [ ] Contact form sends email
- [ ] Newsletter signup works
- [ ] Images display correctly
- [ ] Links work (no 404s)
- [ ] Mobile responsive
- [ ] SSL certificate valid

---

## 🔒 Security Hardening

### Change Default Passwords

Update these immediately:
```php
// Admin password
Database: jhoffkau_admin / GHI@admin2025 → CHANGE THIS!
Email: admin@globalharmonyinitiative.com / GHI@admin2025 → CHANGE THIS!
FTP: admin@globalharmonyinitiative.com / GHI@admin2025 → CHANGE THIS!
```

### Protect Sensitive Files

In `.htaccess` (already configured):
```apache
<FilesMatch "^(composer\.(json|lock)|package(-lock)?\.json|\.env|\.git.*|config/.*\.php)$">
    Require all denied
</FilesMatch>
```

### Enable Security Headers

Already in `.htaccess`:
```apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
```

### Set Up Backups

**Automated Daily Backups**:
1. cPanel → Backup Wizard
2. Schedule daily backups
3. Email notifications to admin email
4. Store backups off-server

**Manual Backup Script**:
```bash
#!/bin/bash
# backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/jhoffkau/backups"

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /home/jhoffkau/public_html

# Backup database
mysqldump -u jhoffkau_admin -p'GHI@admin2025' jhoffkau_GHI > $BACKUP_DIR/db_$DATE.sql

# Delete backups older than 30 days
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
```

---

## 📧 Email Configuration Details

### Production SMTP Settings

**Incoming Mail** (IMAP/POP3):
```
Host: mail.globalharmonyinitiative.com
IMAP Port: 993 (SSL)
POP3 Port: 995 (SSL)
Username: admin@globalharmonyinitiative.com
Password: GHI@admin2025
```

**Outgoing Mail** (SMTP):
```
Host: mail.globalharmonyinitiative.com
Port: 465 (SSL)
Username: admin@globalharmonyinitiative.com
Password: GHI@admin2025
Authentication: Yes
```

### Email Testing

```php
// Create test file: email-test.php
<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/email-config.php';

$result = send_email(
    'your-email@example.com',
    'Test Email from GHI',
    '<h1>Test Email</h1><p>If you receive this, email is working!</p>'
);

echo $result ? 'Email sent!' : 'Email failed - check logs';
// DELETE THIS FILE AFTER TESTING!
?>
```

---

## 🔍 Monitoring & Maintenance

### Log Files

**PHP Error Log**:
```
/home/jhoffkau/logs/php_error.log
```

**Apache Error Log** (if accessible):
```
/var/log/apache2/error.log
```

**Application Logs**:
```
/home/jhoffkau/public_html/logs/app.log
```

### Monitor These:

1. **Disk Space**: via cPanel → Disk Usage
2. **Bandwidth**: via cPanel → Bandwidth
3. **Error Logs**: Check weekly for issues
4. **SSL Certificate**: Renews automatically, check annually
5. **Backups**: Verify daily backups complete

### Performance Monitoring

Use tools:
- Google PageSpeed Insights
- GTmetrix
- Pingdom
- UptimeRobot (for downtime alerts)

---

## 🐛 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error

**Causes**:
- Incorrect .htaccess syntax
- PHP memory limit exceeded
- File permission issues

**Solutions**:
```bash
# Check error log
tail -f /home/jhoffkau/logs/php_error.log

# Test .htaccess
# Rename .htaccess temporarily and see if site loads

# Increase PHP limits in .htaccess
php_value memory_limit 256M
php_value max_execution_time 300
```

#### 2. Database Connection Failed

**Check**:
- Database name: `jhoffkau_GHI`
- Username: `jhoffkau_admin`
- Password: correct?
- Database server: `localhost`

**Test**:
```php
<?php
$conn = new mysqli('localhost', 'jhoffkau_admin', 'GHI@admin2025', 'jhoffkau_GHI');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
```

#### 3. Email Not Sending

**Check**:
1. Email account exists in cPanel
2. SMTP credentials correct
3. Port 465 not blocked by firewall
4. PHP mail() function enabled

**Test SMTP**:
Use online SMTP tester with your credentials

#### 4. Images Not Loading

**Check**:
- File permissions: 644 for images
- Directory permissions: 755 for folders
- Correct path in code (use BASE_URL)
- .htaccess not blocking images

#### 5. Admin Dashboard 403 Forbidden

**Check**:
- `/admin` directory permissions: 755
- `/admin/index.php` permissions: 644
- .htaccess not blocking /admin/

**Fix**:
```bash
chmod 755 /home/jhoffkau/public_html/admin
chmod 644 /home/jhoffkau/public_html/admin/*.php
```

---

## 🔄 Updating the Site

### Minor Updates (content, small fixes)

1. Edit files locally
2. Test locally
3. Upload changed files via FTP
4. Clear cache
5. Test on production

### Major Updates (database changes, new features)

1. **BACKUP FIRST!**
2. Put site in maintenance mode
3. Upload new files
4. Run database migrations
5. Clear all caches
6. Test thoroughly
7. Remove maintenance mode

### Maintenance Mode

Create `/home/jhoffkau/public_html/maintenance.php`:
```php
<!DOCTYPE html>
<html>
<head>
    <title>Maintenance - GHI</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 100px; }
        h1 { color: #667eea; }
    </style>
</head>
<body>
    <h1>Site Under Maintenance</h1>
    <p>We'll be back soon! Thank you for your patience.</p>
</body>
</html>
```

Add to `.htaccess` temporarily:
```apache
RewriteCond %{REQUEST_URI} !^/maintenance\.php$
RewriteRule ^(.*)$ /maintenance.php [R=307,L]
```

---

## 📊 Performance Optimization

### Enabled (Already Configured)

✅ Gzip compression  
✅ Browser caching  
✅ CSS/JS minification (via Vite)  
✅ Image optimization  
✅ Database query caching  
✅ HTTPS/2  

### Additional Recommendations

1. **CDN**: Consider Cloudflare (free)
2. **Image CDN**: Upload images to CDN
3. **Database**: Optimize tables monthly
4. **Cron Jobs**: Set up for maintenance tasks

### Cron Jobs to Set Up

```bash
# Clear cache daily at 2 AM
0 2 * * * cd /home/jhoffkau/public_html && php admin/clear-cache.php

# Database optimization weekly
0 3 * * 0 mysqlcheck -u jhoffkau_admin -p'GHI@admin2025' --optimize --all-databases

# Backup daily at 1 AM
0 1 * * * /home/jhoffkau/scripts/backup.sh
```

---

## ✅ Post-Deployment Checklist

### Immediate (Day 1)
- [ ] All pages load correctly
- [ ] Admin dashboard accessible
- [ ] Contact form sends emails
- [ ] Newsletter signup works
- [ ] SSL certificate installed
- [ ] Google Analytics tracking (if applicable)
- [ ] Social media links work

### Week 1
- [ ] Monitor error logs daily
- [ ] Check email deliverability
- [ ] Test all forms
- [ ] Verify backups running
- [ ] Check site speed
- [ ] Test on multiple devices/browsers

### Month 1
- [ ] Review analytics
- [ ] Optimize slow pages
- [ ] Update any outdated content
- [ ] Check for broken links
- [ ] Review security logs
- [ ] Plan content updates

---

## 🆘 Emergency Contacts

### Hosting Support
**Provider**: [Your hosting provider]  
**Support**: [Support URL/Phone]  
**Account**: jhoffkau  

### Domain Support
**Registrar**: [Domain registrar]  
**Support**: [Support URL]  

### Developer Support
**Email**: [Your email]  
**Phone**: [Your phone]  

---

## 📚 Additional Resources

### Documentation
- **Full Documentation**: See `/ENHANCEMENTS_DOCUMENTATION.md`
- **Quick Start**: See `/QUICK_START_GUIDE.md`
- **Changelog**: See `/CHANGELOG.md`

### External Resources
- [cPanel Documentation](https://docs.cpanel.net/)
- [PHP Manual](https://www.php.net/manual/en/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Apache .htaccess Guide](https://httpd.apache.org/docs/current/howto/htaccess.html)

---

## 🎉 Deployment Complete!

Once all steps are completed:

1. ✅ Site is live at https://www.globalharmonyinitiative.com
2. ✅ Admin dashboard at https://www.globalharmonyinitiative.com/admin
3. ✅ SSL certificate installed and working
4. ✅ Email sending/receiving working
5. ✅ Backups configured
6. ✅ Monitoring set up

**Congratulations! Your site is now in production!** 🚀

---

**Last Updated**: November 11, 2025  
**Version**: 1.0.0  
**Environment**: Production Ready  
**Status**: ✅ Complete

