# Apache/WAMP Troubleshooting Guide

## Current Issue: ERR_CONNECTION_REFUSED

Your Composer dependencies are installed correctly, so the issue is likely that **Apache is not running**.

---

## 🔍 **Step-by-Step Diagnosis**

### **Step 1: Check WAMP Status**

Look at your Windows system tray (bottom-right, near the clock):

1. Find the **WAMP icon** (looks like a speedometer/gauge)
2. Note its color:
   - 🟢 **GREEN** = All services running
   - 🟡 **ORANGE** = Partially running (usually Apache or MySQL down)
   - 🔴 **RED** = Services stopped
   - ⚪ **WHITE/Not visible** = WAMP not started

**What to do:**
- If **NOT GREEN**: Left-click WAMP icon → "Start All Services"
- Wait 10-30 seconds for services to start
- Icon should turn green

---

### **Step 2: Test Basic Connectivity**

Open your browser and test these URLs **in order**:

#### Test 1: WAMP Homepage
```
http://localhost
```
**Expected**: Should see WAMP Server homepage (orange/white page with "Server Configuration" and project list)

**If this fails**: Apache is definitely not running → Go to Step 3

#### Test 2: Simple PHP Test
```
http://localhost/GHI/test-simple.php
```
**Expected**: Should see "PHP is working!" with version info

**If this fails but Test 1 works**: PHP is not configured → Check PHP version in WAMP

#### Test 3: Full Test with Dependencies
```
http://localhost/GHI/test.php
```
**Expected**: Should see phpinfo() page

**If this fails but Test 2 works**: Possible PHP configuration issue

#### Test 4: Your Homepage
```
http://localhost/GHI
```
or
```
http://localhost/GHI/index.php
```

---

### **Step 3: Start WAMP Manually**

If WAMP icon is red or orange:

#### Start Apache:
1. Left-click WAMP icon
2. Hover: **Apache** → **Service** → **Start/Resume Service**
3. Wait 5-10 seconds

#### Start MySQL:
1. Left-click WAMP icon
2. Hover: **MySQL** → **Service** → **Start/Resume Service**
3. Wait 5-10 seconds

#### Verify:
WAMP icon should turn green

---

### **Step 4: Check Apache Error Log**

If Apache won't start:

1. Left-click WAMP icon
2. Hover: **Apache** → **Apache error log**
3. Look at the **last 20 lines** of the file
4. Common errors:
   - **"(OS 10048) Only one usage of each socket address"** → Port 80 in use
   - **"Cannot load modules"** → PHP/Apache version mismatch
   - **"Permission denied"** → Run WAMP as Administrator

---

### **Step 5: Check Port 80 Availability**

Port 80 might be used by another program:

1. Open **PowerShell as Administrator** (`Win + X` → "Windows PowerShell (Admin)")
2. Run:
   ```powershell
   netstat -ano | findstr :80
   ```
3. If you see output, port 80 is in use

**Common culprits:**
- IIS (World Wide Web Publishing Service)
- Skype
- VMware
- SQL Server Reporting Services
- Another Apache instance

**To free port 80:**
1. Press `Win + R` → type `services.msc` → Enter
2. Find **"World Wide Web Publishing Service"** (if exists)
3. Right-click → **Stop**
4. Right-click → **Properties** → Set "Startup type" to **Disabled**
5. Restart WAMP

---

### **Step 6: Check Windows Firewall**

Firewall might be blocking Apache:

1. Press `Win + R` → type `firewall.cpl` → Enter
2. Click **"Allow an app or feature through Windows Defender Firewall"**
3. Click **"Change settings"** (requires admin)
4. Look for **"Apache HTTP Server"** or **"httpd.exe"**
5. Ensure **both** "Private" and "Public" are checked

If not in list:
1. Click **"Allow another app..."**
2. Browse to: `C:\wamp64\bin\apache\apache2.4.xx\bin\httpd.exe` (adjust version)
3. Add it and check both boxes

---

### **Step 7: Check PHP Version**

Wrong PHP version can cause issues:

1. Left-click WAMP icon
2. Hover: **PHP** → **Version**
3. Select **PHP 8.2.26** (or your installed version that matches composer.json)

Your project requires: **PHP 8.2+**

---

### **Step 8: Run WAMP as Administrator**

Sometimes WAMP needs admin rights:

1. Exit WAMP completely:
   - Right-click WAMP icon → **Exit**
2. Go to: `C:\wamp64\`
3. Right-click **`wampmanager.exe`**
4. Select **"Run as administrator"**
5. Allow UAC prompt
6. Wait for services to start

---

### **Step 9: Restart Windows Services**

Sometimes services get stuck:

1. Press `Win + R` → type `services.msc` → Enter
2. Find services starting with **"wampapache"** or **"wampmysqld"**
3. Right-click each → **Restart**
4. Check WAMP icon color

---

### **Step 10: Check Virtual Hosts**

Make sure there's no conflicting virtual host:

1. Left-click WAMP icon
2. Hover: **Apache** → **httpd-vhosts.conf**
3. Look for any `<VirtualHost *:80>` entries
4. Make sure none are redirecting localhost incorrectly

---

## 🧪 **Quick Diagnostic Commands**

Run these in PowerShell (Admin) to gather info:

```powershell
# Check if Apache process is running
Get-Process -Name "httpd" -ErrorAction SilentlyContinue

# Check port 80 usage
netstat -ano | findstr :80

# Check Apache service
Get-Service -Name "wampapache*" -ErrorAction SilentlyContinue

# Check MySQL service  
Get-Service -Name "wampmysqld*" -ErrorAction SilentlyContinue

# Test PHP from command line
php -v
```

---

## 📊 **Expected Output**

### When Working:
```
# WAMP icon: GREEN
# http://localhost → Shows WAMP homepage
# http://localhost/GHI/test-simple.php → Shows "PHP is working!"
# http://localhost/GHI → Shows your homepage
```

---

## 🆘 **Still Not Working?**

### Provide This Information:

1. **WAMP Icon Color**: (Green/Orange/Red/White)
2. **Port 80 Check Result**: Output from `netstat -ano | findstr :80`
3. **Apache Process Check**: Output from `Get-Process -Name "httpd"`
4. **Apache Error Log**: Last 10 lines
5. **What you see when visiting**: `http://localhost`

---

## 🎯 **90% Solution Checklist**

Try these in order (stop when it works):

1. ✅ **Start WAMP** (WAMP icon → "Start All Services")
2. ✅ **Test**: `http://localhost` (should see WAMP homepage)
3. ✅ **Test**: `http://localhost/GHI/test-simple.php`
4. ✅ **Stop IIS** (if running)
5. ✅ **Run WAMP as Administrator**
6. ✅ **Restart computer** (fixes 99% of weird issues)

---

**Most likely solution**: Start WAMP services and ensure the icon is green! 🟢

