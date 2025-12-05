# Fix: localhost refused to connect (ERR_CONNECTION_REFUSED)

## ❌ Current Error
```
This site can't be reached
localhost refused to connect
ERR_CONNECTION_REFUSED
```

## 🔍 Diagnosis
This error means **Apache web server is not running** or **port 80 is blocked/in use**.

---

## ✅ Solution Steps

### **Step 1: Check if WAMP is Running**

Look at your **system tray** (bottom right corner of Windows):

1. **Find the WAMP icon** (looks like a gauge/meter)
2. **Check the color**:
   - 🟢 **GREEN** = All services running ✅
   - 🟡 **ORANGE/YELLOW** = Some services offline ⚠️
   - 🔴 **RED** = All services offline ❌
   - ⚪ **WHITE/OFFLINE** = WAMP not started ❌

---

### **Step 2: Start WAMP Services**

#### If WAMP Icon is Red, Orange, or White:

1. **Left-click** on WAMP icon
2. Click **"Start All Services"** or **"Restart All Services"**
3. Wait 10-30 seconds
4. Icon should turn **GREEN**

#### If that doesn't work:

1. **Right-click** WAMP icon
2. Select **"Restart All Services"**
3. Wait for icon to turn green

---

### **Step 3: Test MySQL Separately**

Sometimes MySQL fails to start, which can block Apache:

1. **Left-click** WAMP icon
2. Hover over **"MySQL"** → **"Service"**
3. Click **"Start/Resume Service"**
4. Wait for it to start

Then do the same for Apache:
1. Hover over **"Apache"** → **"Service"**
2. Click **"Start/Resume Service"**

---

### **Step 4: Check Port 80 (Common Issue)**

Port 80 might be used by another program (Skype, IIS, VMware, etc.)

#### Find what's using Port 80:

1. Press **`Win + X`** → Select **"Windows PowerShell (Admin)"** or **"Command Prompt (Admin)"**
2. Run this command:
   ```cmd
   netstat -ano | findstr :80
   ```
3. Look for lines with `:80` in the output

#### Common Port 80 conflicts:
- **IIS (Internet Information Services)** - Windows built-in web server
- **Skype** - Uses port 80/443
- **VMware** - Can use port 80
- **SQL Server Reporting Services**
- **World Wide Web Publishing Service**

---

### **Step 5: Stop IIS (If Running)**

IIS commonly conflicts with WAMP:

1. Press **`Win + R`**
2. Type: **`services.msc`** and press Enter
3. Find **"World Wide Web Publishing Service"**
4. Right-click → **"Stop"**
5. Right-click again → **"Properties"** → Set **"Startup type"** to **"Manual"** or **"Disabled"**
6. Click **"OK"**
7. Restart WAMP

---

### **Step 6: Change Apache Port (Alternative)**

If you can't free port 80, change Apache to use a different port:

1. **Left-click** WAMP icon
2. Hover over **"Apache"** → **"httpd.conf"**
3. Click to open the file
4. Find line with: `Listen 80`
5. Change to: `Listen 8080`
6. Find line with: `ServerName localhost:80`
7. Change to: `ServerName localhost:8080`
8. **Save** the file
9. **Restart Apache**
10. Access site at: `http://localhost:8080/GHI`

---

### **Step 7: Check Firewall**

Windows Firewall might be blocking Apache:

1. Press **`Win + R`**
2. Type: **`firewall.cpl`** and press Enter
3. Click **"Allow an app or feature through Windows Defender Firewall"**
4. Click **"Change settings"** (needs admin)
5. Find **"Apache HTTP Server"** or **"httpd.exe"**
6. Check **both** "Private" and "Public" boxes
7. If not in list, click **"Allow another app..."** → Browse to `C:\wamp64\bin\apache\apache2.x.xx\bin\httpd.exe`
8. Click **"OK"**

---

### **Step 8: Check WAMP Logs**

If Apache still won't start:

1. **Left-click** WAMP icon
2. Hover over **"Apache"** → **"Apache error log"**
3. Click to open
4. Look at the **bottom** of the file for recent errors
5. Common errors:
   - "Port already in use" → See Step 4-5
   - "Permission denied" → Run WAMP as Administrator
   - "Cannot load modules" → PHP/Apache version mismatch

---

### **Step 9: Run WAMP as Administrator**

1. **Right-click** WAMP icon in system tray
2. Click **"Exit"**
3. Go to: `C:\wamp64\`
4. **Right-click** `wampmanager.exe`
5. Select **"Run as administrator"**
6. Wait for services to start

---

## 🧪 Test if Apache is Running

### Quick Test:
1. Open browser
2. Go to: `http://localhost`
3. You should see **WAMP Server homepage** (orange/white page with projects list)
4. If you see this, Apache is running ✅

### If WAMP homepage loads:
Then your project should work at:
- `http://localhost/GHI`

---

## 🆘 Still Not Working?

### Collect This Information:

1. **WAMP Icon Color**: (Red/Orange/Green/White)
2. **Port 80 Status**: Run `netstat -ano | findstr :80` and paste result
3. **Apache Error Log**: Last 10 lines from Apache error log
4. **Windows Version**: Press `Win + Pause` to see

### Quick Diagnostic Command:

Run this in PowerShell (Admin):
```powershell
# Check Apache process
Get-Process -Name "httpd" -ErrorAction SilentlyContinue

# Check what's using port 80
netstat -ano | findstr :80

# Check Apache service status
Get-Service -Name "wampapache*" -ErrorAction SilentlyContinue
```

---

## 🎯 Most Common Solutions (In Order):

1. ✅ **Start WAMP services** (90% of cases)
2. ✅ **Stop IIS** (if installed)
3. ✅ **Run WAMP as Administrator**
4. ✅ **Check port 80 conflicts**
5. ✅ **Restart computer** (if all else fails)

---

**Start with Step 1** - check if WAMP icon is green. If not, start the services and it should work immediately!

