# 🗄️ Production Database Setup Guide

## ⚠️ Database Connection Error - Quick Fix

Your production database settings (from `config/environment.php`):

```
Host:     localhost
Database: jhoffkau_GHI
Username: jhoffkau_admin
Password: GHI@admin2025
```

---

## 🔍 STEP 1: Test Database Connection

I've created a test file: `test-production-db.php`

### **Upload and Run Test:**

1. Upload `test-production-db.php` to your server root
2. Visit: `https://www.globalharmonyinitiative.com/test-production-db.php`
3. Check the results
4. **Delete the file after testing!** (security risk)

This will tell you:
- ✅ If MySQL connection works
- ✅ If database exists
- ✅ If tables are imported
- ❌ What's wrong if it fails

---

## 🔧 STEP 2: Fix Database Issues

### **Issue A: Database Doesn't Exist**

**Solution: Create Database in cPanel**

1. **Login to cPanel**
2. Go to **"MySQL Databases"**
3. **Create New Database:**
   - Database Name: `jhoffkau_GHI`
   - Click "Create Database"
   
4. **Create Database User:**
   - Username: `jhoffkau_admin`
   - Password: `GHI@admin2025`
   - Click "Create User"
   
5. **Add User to Database:**
   - User: `jhoffkau_admin`
   - Database: `jhoffkau_GHI`
   - Check: **ALL PRIVILEGES**
   - Click "Make Changes"

6. **Import Your Database:**
   - Go to **phpMyAdmin** in cPanel
   - Select `jhoffkau_GHI` database
   - Click **"Import"** tab
   - Upload your SQL file (from development)
   - Click "Go"

---

### **Issue B: Wrong Database Name**

If the database already exists with a different name:

**Option 1: Find Existing Database Name**

1. Login to cPanel → **MySQL Databases**
2. Check **"Current Databases"** section
3. Look for your GHI database (might be named differently)

**Option 2: Update Environment Config**

If your database is named differently, you have two choices:

1. **Rename the database in cPanel** (to match `jhoffkau_GHI`)
2. **Or update** `config/environment.php` line 80:
   ```php
   'db_name' => 'your_actual_database_name',
   ```

---

### **Issue C: User Doesn't Have Permissions**

**Solution: Grant Permissions**

1. cPanel → **MySQL Databases**
2. Scroll to **"Add User To Database"**
3. Select:
   - User: `jhoffkau_admin`
   - Database: `jhoffkau_GHI`
4. Click "Add"
5. Check **ALL PRIVILEGES**
6. Click "Make Changes"

---

## 📊 STEP 3: Export Database from Development

If you need to move your local database to production:

### **A. Export from Development (WAMP)**

**Method 1: phpMyAdmin (Easiest)**

1. Open: `http://localhost/phpmyadmin`
2. Select database: `global_harmony_initiative`
3. Click **"Export"** tab
4. Keep default settings (Quick export, SQL format)
5. Click **"Go"**
6. Save the `.sql` file

**Method 2: Command Line (Faster)**

```bash
# In your terminal/PowerShell
cd C:\wamp64\bin\mysql\mysql8.x.x\bin

# Export entire database
.\mysqldump.exe -u root -p global_harmony_initiative > ghi_database.sql

# Press Enter (no password for WAMP root)
```

### **B. Import to Production**

**Method 1: cPanel phpMyAdmin**

1. Login to cPanel
2. Click **"phpMyAdmin"**
3. Select database: `jhoffkau_GHI`
4. Click **"Import"** tab
5. Click **"Choose File"** → Select your `.sql` file
6. Click **"Go"**
7. Wait for import to complete

**Method 2: cPanel Terminal (SSH) - Much Faster for Large DBs**

```bash
# Navigate to home directory
cd ~

# Upload your SQL file first (via cPanel File Manager or FTP)
# Then import it:
mysql -u jhoffkau_admin -p jhoffkau_GHI < ghi_database.sql

# Enter password when prompted: GHI@admin2025
```

---

## 🎯 STEP 4: Verify Database Connection

After setup, test again:

1. Visit: `https://www.globalharmonyinitiative.com/test-production-db.php`
2. Should see: ✅ All green checkmarks
3. Should list all your tables
4. **Delete test file!**

Or test the actual website:
- Homepage: `https://www.globalharmonyinitiative.com`
- Admin: `https://www.globalharmonyinitiative.com/admin/`

---

## 🔐 STEP 5: Security (After Database Works)

1. **Delete test file:**
   ```bash
   rm ~/public_html/test-production-db.php
   ```

2. **Verify .htaccess protects config:**
   - Already protected by your .htaccess file ✅

3. **Check database user permissions:**
   - Should only have access to `jhoffkau_GHI` database

---

## 📋 Quick Checklist

```
☐ Create database: jhoffkau_GHI
☐ Create user: jhoffkau_admin with password: GHI@admin2025
☐ Grant ALL PRIVILEGES to user on database
☐ Export database from development
☐ Import SQL file to production database
☐ Run test-production-db.php to verify
☐ Delete test-production-db.php
☐ Test website and admin panel
```

---

## 🚨 Common Error Messages

### **"Access denied for user 'jhoffkau_admin'@'localhost'"**
**Fix:** User doesn't exist or wrong password
- Create user in cPanel MySQL Databases
- Or check password is: `GHI@admin2025`

### **"Unknown database 'jhoffkau_GHI'"**
**Fix:** Database doesn't exist
- Create it in cPanel MySQL Databases

### **"No tables found"**
**Fix:** Database is empty
- Import your SQL file

### **"Can't connect to MySQL server"**
**Fix:** MySQL service issue
- Contact hosting support
- Or check if MySQL is running in cPanel

---

## 🔄 Alternative: Use Different Database Name

If your host uses a different naming convention:

1. **Check what databases exist:**
   - cPanel → MySQL Databases → Current Databases
   
2. **Update environment.php:**
   ```php
   // Line 80 in config/environment.php
   'db_name' => 'your_actual_database_name',
   'db_user' => 'your_actual_username',
   'db_pass' => 'your_actual_password',
   ```

---

## 💡 Pro Tips

1. **Use SSH to import large databases** (faster than phpMyAdmin)
2. **Compress SQL files** before uploading:
   ```bash
   gzip ghi_database.sql
   # Upload ghi_database.sql.gz, then:
   gunzip ghi_database.sql.gz
   mysql -u user -p database < ghi_database.sql
   ```
3. **Backup before importing:**
   ```bash
   mysqldump -u jhoffkau_admin -p jhoffkau_GHI > backup.sql
   ```

---

## ✅ Database Should Work After This!

Once database is set up:
- Environment auto-detection will use production settings
- Website will connect automatically
- Admin panel will load data

**Total Time: 5-10 minutes** (depending on import size)

