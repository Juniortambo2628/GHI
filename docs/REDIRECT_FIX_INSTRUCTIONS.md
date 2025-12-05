# Redirect Issue Fix - localhost/GHI → localhost/

## Issue
Accessing `http://localhost/GHI` redirects to `http://localhost/`

## Fix Applied ✅
Added `DirectoryIndex index.php index.html` to `.htaccess` file.

---

## Clear Browser Cache (IMPORTANT)

The redirect is likely cached in your browser. Follow these steps:

### **Option 1: Hard Refresh (Quick)**
1. Open `http://localhost/GHI` in your browser
2. Press:
   - **Chrome/Edge**: `Ctrl + Shift + R` or `Ctrl + F5`
   - **Firefox**: `Ctrl + Shift + R` or `Ctrl + F5`
   - **Safari**: `Cmd + Shift + R`

### **Option 2: Clear Browser Cache (Thorough)**

#### Chrome/Edge:
1. Press `Ctrl + Shift + Delete`
2. Select "Time range" → "All time"
3. Check "Cached images and files"
4. Click "Clear data"

#### Firefox:
1. Press `Ctrl + Shift + Delete`
2. Select "Time range" → "Everything"
3. Check "Cache"
4. Click "Clear Now"

### **Option 3: Use Incognito/Private Mode (Test)**
1. Open a new Incognito/Private window
2. Navigate to `http://localhost/GHI`
3. If it works here, the issue is browser cache

---

## Restart Apache (If Needed)

If clearing cache doesn't work:

1. Open **WAMP** tray icon (green)
2. Click → **Apache** → **Service** → **Restart Service**
3. Wait for WAMP to turn green again
4. Try accessing `http://localhost/GHI` again

---

## Verify Configuration

### Test URLs (Try these in order):
1. ✅ `http://localhost/GHI` - Should load homepage
2. ✅ `http://localhost/GHI/` - Should load homepage  
3. ✅ `http://localhost/GHI/index.php` - Should load homepage
4. ✅ `http://localhost/GHI/events` - Should load events page

### Expected Behavior:
- All URLs above should work
- No redirects to `localhost/` (root)
- Homepage displays correctly

---

## If Issue Persists

### Check WAMP Document Root:
1. Open WAMP tray icon
2. Click → **Apache** → **httpd.conf**
3. Search for `DocumentRoot`
4. Should be: `DocumentRoot "c:/wamp64/www"`
5. If different, change it and restart Apache

### Check Virtual Hosts:
1. Open WAMP tray icon
2. Click → **Apache** → **httpd-vhosts.conf**
3. Make sure there's no virtual host for `localhost` that's interfering

### Check Windows Hosts File:
1. Open: `C:\Windows\System32\drivers\etc\hosts`
2. Look for any `localhost` entries
3. Should only have: `127.0.0.1 localhost`

---

## Contact for Help

If none of the above works, please provide:
1. The exact URL you're typing
2. What browser you're using
3. Screenshot of the redirect
4. WAMP DocumentRoot value from httpd.conf

---

**Most Common Solution**: Hard refresh with `Ctrl + Shift + R` or clear browser cache!

