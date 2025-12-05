# 🚀 SSH Quick Setup - Easiest Method (cPanel Terminal)

## ⚡ EASIEST: Use cPanel Built-in Terminal (No Setup Required!)

### **Step 1: Access Terminal in cPanel**

1. **Login to cPanel**
   - URL: `https://globalharmonyinitiative.com:2083`
   - Username: `admin@globalharmonyinitiative.com`
   - Password: `GHI@admin2025`

2. **Find Terminal**
   - Scroll down to **"Advanced"** section
   - Click **"Terminal"** (icon looks like `>_` or a black screen)
   - A terminal window will open in your browser
   - **No setup needed!** ✅

### **Step 2: Run Permission Commands**

Copy and paste this **one command** (it will fix everything):

```bash
cd ~/public_html && find . -type d -exec chmod 755 {} \; && find . -type f -exec chmod 644 {} \; && chmod 775 uploads uploads/images logs cache && echo "✅ All permissions fixed!"
```

**Press Enter**

**Time: 5-10 seconds** ⚡

That's it! Your permissions are now set correctly.

---

## 📋 Step-by-Step Breakdown (If You Want to Run Individually)

If you prefer to run commands one at a time:

```bash
# 1. Navigate to your website directory
cd ~/public_html

# 2. Set all directories to 755
find . -type d -exec chmod 755 {} \;

# 3. Set all files to 644
find . -type f -exec chmod 644 {} \;

# 4. Set writable directories to 775
chmod 775 uploads
chmod 775 uploads/images
chmod 775 logs
chmod 775 cache

# 5. Verify
echo "✅ Permissions set!"
```

---

## 🔍 Verify Permissions Were Set

Run this to check:

```bash
ls -la ~/public_html | head -20
```

You should see:
```
drwxr-xr-x  - admin   (755 for directories)
-rw-r--r--  - index.php   (644 for files)
drwxrwxr-x  - uploads     (775 for writable)
```

---

## ⚠️ If You Don't See "Terminal" in cPanel

Some hosts disable Terminal. Here are alternatives:

### **Option A: Use Terminal from cPanel Menu**
- Look in: **"Advanced"** section
- Or search: Type "Terminal" in cPanel search bar

### **Option B: Enable SSH Access**
1. In cPanel, search for **"SSH Access"**
2. Click **"Manage SSH Keys"**
3. Follow prompts to enable

### **Option C: Manual File Manager Method**

If SSH isn't available, use this in File Manager:

1. **For Root Directory:**
   - Select `public_html` folder
   - Right-click → Permissions → `755`

2. **For Each Main Folder** (one at a time):
   ```
   admin/    → 755
   config/   → 755
   includes/ → 755
   css/      → 755
   js/       → 755
   dist/     → 755
   vendor/   → 755
   api/      → 755
   templates/→ 755
   img/      → 755
   Logo/     → 755
   Banners-and-portraits/ → 755
   ```

3. **For Writable Folders:**
   ```
   uploads/  → 775
   logs/     → 775
   cache/    → 775
   ```

4. **For Individual Files** (spot check):
   ```
   index.php → 644
   .htaccess → 644
   ```

---

## 🎯 Quick Reference

### **The Magic One-Liner:**
```bash
cd ~/public_html && find . -type d -exec chmod 755 {} \; && find . -type f -exec chmod 644 {} \; && chmod 775 uploads uploads/images logs cache && echo "✅ Done!"
```

### **What it does:**
- `cd ~/public_html` - Go to your website folder
- `find . -type d -exec chmod 755 {} \;` - Set ALL directories to 755
- `find . -type f -exec chmod 644 {} \;` - Set ALL files to 644
- `chmod 775 uploads ...` - Set writable folders to 775
- `echo "✅ Done!"` - Show success message

---

## ✅ After Running Commands

Your permissions will be:
- 📁 All directories: `755` (rwxr-xr-x)
- 📄 All files: `644` (rw-r--r--)
- 📂 Writable folders: `775` (rwxrwxr-x)

Perfect for production! 🎉

---

## 🚨 Common Issues

### **Issue: "Permission denied"**
**Solution:** Make sure you're in the right directory:
```bash
cd ~/public_html
pwd  # Should show: /home/jhoffkau/public_html
```

### **Issue: "No such file or directory: uploads"**
**Solution:** Create the directories first:
```bash
mkdir -p uploads/images logs cache
chmod 775 uploads uploads/images logs cache
```

### **Issue: "Terminal not found in cPanel"**
**Solution:** Contact your host to enable SSH/Terminal access, or use the manual File Manager method above.

---

## 📊 Time Saved

| Method | Time |
|--------|------|
| **SSH One-Liner** | **10 seconds** ⚡⚡⚡⚡⚡ |
| Manual File Manager | 10-15 minutes |
| FileZilla | 20-30 minutes |

**SSH is 100x faster!** 🚀

