# 🚀 QUICK PERMISSIONS SETUP - cPanel Method

## ⚡ FASTEST METHODS (Seconds vs Minutes!)

### **METHOD 1: cPanel File Manager (RECOMMENDED) - 30 seconds total**

This is **10x faster** than FileZilla!

#### **Step-by-Step:**

1. **Login to cPanel**
   - URL: `https://globalharmonyinitiative.com:2083`
   - Username: `admin@globalharmonyinitiative.com`
   - Password: `GHI@admin2025`

2. **Open File Manager**
   - Click "File Manager" in Files section
   - Navigate to `/home/jhoffkau/public_html`

3. **Set Directory Permissions (755)**
   
   **Select All Directories:**
   - Click "Settings" (top right)
   - Enable "Show Hidden Files (dotfiles)"
   - Click on first folder
   - Hold `Shift` and click last folder
   - Or: Hold `Ctrl` and click each folder individually
   
   **Apply Permissions:**
   - Right-click selected folders
   - Choose "Change Permissions"
   - Check these boxes:
     ```
     Owner:  ☑ Read  ☑ Write  ☑ Execute
     Group:  ☑ Read  ☐ Write  ☑ Execute
     World:  ☑ Read  ☐ Write  ☑ Execute
     ```
   - **Or enter numeric value: `755`**
   - ☑ **Check "Recurse into subdirectories"**
   - ☑ **Check "Apply to directories only"**
   - Click "Change Permissions"
   - **Time: ~10 seconds**

4. **Set File Permissions (644)**
   
   **Select All Files:**
   - Select any file (or press Ctrl+A for all)
   
   **Apply Permissions:**
   - Right-click selected files
   - Choose "Change Permissions"
   - Check these boxes:
     ```
     Owner:  ☑ Read  ☑ Write  ☐ Execute
     Group:  ☑ Read  ☐ Write  ☐ Execute
     World:  ☑ Read  ☐ Write  ☐ Execute
     ```
   - **Or enter numeric value: `644`**
   - ☑ **Check "Recurse into subdirectories"**
   - ☑ **Check "Apply to files only"**
   - Click "Change Permissions"
   - **Time: ~10 seconds**

5. **Set Special Permissions for Writable Directories**
   
   Navigate to and set **755** or **775** for:
   - `uploads/` → **775**
   - `uploads/images/` → **775**
   - `logs/` → **775**
   - `cache/` → **775**
   
   For each:
   - Right-click folder
   - Change Permissions
   - Enter: `775`
   - ☑ Recurse into subdirectories
   - Apply

---

### **METHOD 2: SSH Terminal (FASTEST!) - 5 seconds total**

If you have SSH access, this is **instant**!

#### **Get SSH Access:**
1. cPanel → "Terminal" or "SSH Access"
2. Or use PuTTY/Terminal to connect:
   ```
   ssh admin@globalharmonyinitiative.com
   ```

#### **Run These Commands:**

```bash
# Navigate to your website root
cd /home/jhoffkau/public_html

# Set all directories to 755
find . -type d -exec chmod 755 {} \;

# Set all files to 644
find . -type f -exec chmod 644 {} \;

# Set writable directories to 775
chmod 775 uploads uploads/images logs cache

# Set PHP files to 644 (if not already)
find . -name "*.php" -exec chmod 644 {} \;

# Set .htaccess to 644
chmod 644 .htaccess

# Done! (Takes ~5 seconds total)
```

**Time: 5 seconds for entire website!**

---

### **METHOD 3: FileZilla (If You Must) - Faster Tips**

If you prefer FileZilla, here's how to do it faster:

#### **Bulk Permission Change:**

1. **Connect to FTP:**
   - Host: `ftp.globalharmonyinitiative.com`
   - Username: `admin@globalharmonyinitiative.com`
   - Password: `GHI@admin2025`
   - Port: `21`

2. **Select Multiple Items:**
   - Navigate to `/public_html`
   - Press `Ctrl + A` to select ALL files and folders

3. **Change Permissions:**
   - Right-click selection
   - Choose "File permissions..."
   - Set numeric value: `755` for directories
   - ☑ Check "Recurse into subdirectories"
   - ☑ Check "Apply to directories only"
   - Click OK
   - **Wait... (this will take 2-5 minutes)**

4. **Repeat for Files:**
   - Select all again (`Ctrl + A`)
   - Right-click → "File permissions..."
   - Set numeric value: `644`
   - ☑ Recurse into subdirectories
   - ☑ Check "Apply to files only"
   - Click OK
   - **Wait... (another 2-5 minutes)**

**Time: 5-10 minutes**

---

## 📊 TIME COMPARISON

| Method | Time | Difficulty | Speed |
|--------|------|------------|-------|
| **SSH Terminal** | **5 seconds** | Easy | ⚡⚡⚡⚡⚡ |
| **cPanel File Manager** | **30 seconds** | Very Easy | ⚡⚡⚡⚡ |
| **FileZilla Bulk** | **5-10 minutes** | Easy | ⚡⚡ |
| **FileZilla One-by-One** | **30+ minutes** | Tedious | ⚡ |

---

## 🎯 RECOMMENDED PERMISSIONS

### **Standard Permissions:**
```
Directories:     755  (rwxr-xr-x)
PHP Files:       644  (rw-r--r--)
CSS/JS Files:    644  (rw-r--r--)
Images:          644  (rw-r--r--)
.htaccess:       644  (rw-r--r--)
```

### **Writable Directories:**
```
uploads/         775  (rwxrwxr-x)
uploads/images/  775  (rwxrwxr-x)
logs/            775  (rwxrwxr-x)
cache/           775  (rwxrwxr-x)
```

### **Never Use:**
```
❌ 777 - TOO PERMISSIVE (security risk!)
❌ 666 - Too permissive for files
```

---

## 🔒 SECURITY BEST PRACTICES

### **After Setting Permissions:**

1. **Verify .htaccess is protected:**
   ```
   chmod 644 .htaccess
   ```

2. **Protect config files:**
   ```
   chmod 644 config/*.php
   ```

3. **Check admin access:**
   ```
   chmod 644 admin/*.php
   ```

4. **Verify writable directories:**
   ```bash
   # These should be 775
   ls -la uploads/
   ls -la logs/
   ls -la cache/
   ```

---

## ⚡ QUICK REFERENCE COMMANDS

### **For cPanel Terminal/SSH:**

```bash
# All-in-one permission fix
cd /home/jhoffkau/public_html && \
find . -type d -exec chmod 755 {} \; && \
find . -type f -exec chmod 644 {} \; && \
chmod 775 uploads uploads/images logs cache && \
echo "✅ Permissions fixed!"
```

**Copy and paste this single command - Done in 5 seconds!**

---

## 🚨 TROUBLESHOOTING

### **Issue: "Permission denied" in SSH**
**Solution:** Your user might not have access. Use cPanel File Manager instead.

### **Issue: "Cannot write to uploads/"**
**Solution:** Set uploads directory to 775:
```bash
chmod 775 uploads
chmod 775 uploads/images
```

### **Issue: "Internal Server Error after permission change"**
**Solution:** You might have set 777. Change back to:
- Directories: 755
- Files: 644

### **Issue: FileZilla connection timeout**
**Solution:** 
1. Use Passive Mode: Edit → Settings → Connection → FTP → Passive
2. Or switch to cPanel File Manager (much faster anyway!)

---

## 🎯 RECOMMENDED: Use cPanel File Manager

**Why?**
- ✅ Fast (30 seconds vs 10 minutes)
- ✅ No additional software needed
- ✅ Direct server access
- ✅ Visual interface
- ✅ Bulk operations
- ✅ No connection timeouts
- ✅ Instant feedback

**FileZilla is great for:**
- Transferring files
- Downloading backups
- Quick edits

**But for bulk permissions, cPanel is 20x faster!**

---

## ✅ VERIFICATION

After setting permissions, verify:

1. **Website loads:** `https://www.globalharmonyinitiative.com`
2. **Admin works:** `https://www.globalharmonyinitiative.com/admin/`
3. **Images display:** Check homepage images
4. **No errors:** Check for 500/403 errors

---

## 🎉 DONE!

Your permissions should now be set correctly in under 1 minute!

**Recommended Method:**
1. Login to cPanel
2. Open File Manager
3. Select all directories → 755 → Recurse → Directories only
4. Select all files → 644 → Recurse → Files only
5. Set uploads, logs, cache to 775

**Total Time: 30 seconds** ⚡

vs FileZilla: 10+ minutes ❌

