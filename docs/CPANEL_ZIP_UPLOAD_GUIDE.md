# 🚀 cPanel Zip Upload Guide - FASTER METHOD!

## Much Faster Than FTP! ⚡

**Estimated Time**: 2-5 minutes (vs 10-20 minutes via FTP)

---

## Step 1: Create Zip File (Windows)

### **Option A: PowerShell Script (Recommended)**

1. Open PowerShell in your project directory
2. Run this command:

```powershell
# This will create GHI-production.zip with all necessary files
Compress-Archive -Path `
  admin, `
  assets, `
  Banners-and-portraits, `
  config, `
  css, `
  dist, `
  includes, `
  js, `
  lib, `
  Logo, `
  src, `
  vendor, `
  .htaccess, `
  404.php, `
  about.php, `
  causes.php, `
  coming-soon-donate.php, `
  coming-soon-get-involved.php, `
  composer.json, `
  composer.lock, `
  contact.php, `
  events.php, `
  impact.php, `
  index.php, `
  initiatives.php, `
  stories.php `
  -DestinationPath GHI-production.zip -Force
```

### **Option B: Manual Selection (If PowerShell fails)**

1. Select these folders (hold Ctrl and click):
   - admin/
   - assets/
   - Banners-and-portraits/
   - config/
   - css/
   - dist/
   - includes/
   - js/
   - lib/
   - Logo/
   - src/
   - vendor/

2. Select these files (hold Ctrl and click):
   - .htaccess
   - 404.php
   - about.php
   - causes.php
   - coming-soon-donate.php
   - coming-soon-get-involved.php
   - composer.json
   - composer.lock
   - contact.php
   - events.php
   - impact.php
   - index.php
   - initiatives.php
   - stories.php

3. Right-click → Send to → Compressed (zipped) folder
4. Name it: `GHI-production.zip`

### **IMPORTANT - DO NOT Include:**
- ❌ node_modules/
- ❌ .env
- ❌ *.md files (optional)
- ❌ package.json (optional)
- ❌ package-lock.json (optional)
- ❌ vite.config.js (optional)
- ❌ .htaccess.development
- ❌ .htaccess.production

**Expected Zip Size**: 30-50 MB (depending on images)

---

## Step 2: Upload to cPanel

### **A. Login to cPanel**
1. Go to: `https://globalharmonyinitiative.com:2083` or your cPanel URL
2. **Username**: admin@globalharmonyinitiative.com (or provided username)
3. **Password**: GHI@admin2025 (or your cPanel password)

### **B. Navigate to File Manager**
1. In cPanel dashboard, find **"Files"** section
2. Click **"File Manager"**
3. Navigate to: `/home/jhoffkau/public_html`

### **C. Clear Existing Files (If Needed)**
⚠️ **CAUTION**: Only do this if you want to replace everything

1. Select all files/folders in `public_html`
2. Click **"Delete"**
3. Confirm deletion

### **D. Upload Zip File**
1. Click **"Upload"** button (top toolbar)
2. Click **"Select File"** or drag and drop
3. Select `GHI-production.zip`
4. Wait for upload to complete (progress bar will show)
5. **Upload Time**: 1-3 minutes (depending on connection)

### **E. Extract Zip File**
1. Go back to File Manager
2. Find `GHI-production.zip` in the file list
3. Right-click on the zip file
4. Select **"Extract"**
5. Confirm extraction path: `/home/jhoffkau/public_html`
6. Click **"Extract File(s)"**
7. **Extraction Time**: 10-30 seconds

### **F. Move Files to Root (If Extracted to Subfolder)**
If files extracted to a subfolder (like `GHI-production/`):
1. Open the subfolder
2. Select all files and folders inside
3. Click **"Move"**
4. Set destination: `/home/jhoffkau/public_html`
5. Click **"Move Files"**

### **G. Delete Zip File (Cleanup)**
1. Select `GHI-production.zip`
2. Click **"Delete"**
3. Confirm

---

## Step 3: Create Required Directories

### **In File Manager:**

1. Click **"+ Folder"** button
2. Create these folders (if they don't exist):
   - `uploads`
   - `uploads/images`
   - `logs`
   - `cache`

3. **Set Permissions** for each:
   - Right-click folder → **"Change Permissions"**
   - Set to **755** (read, write, execute for owner)
   - Check: Owner: Read, Write, Execute
   - Check: Group: Read, Execute
   - Check: World: Read, Execute
   - Click **"Change Permissions"**

---

## Step 4: Verify Upload

### **Check Files:**
1. In File Manager, verify these directories exist:
   - ✅ admin/
   - ✅ config/
   - ✅ dist/
   - ✅ includes/
   - ✅ vendor/
   - ✅ Logo/
   - ✅ Banners-and-portraits/
   - ✅ etc.

2. Check root files:
   - ✅ .htaccess
   - ✅ index.php
   - ✅ 404.php
   - ✅ composer.json
   - ✅ etc.

### **Test Website:**
1. Open browser
2. Visit: `https://www.globalharmonyinitiative.com`
3. Check:
   - ✅ Homepage loads
   - ✅ Images display
   - ✅ Navigation works
   - ✅ Animations are smooth

### **Test Admin:**
1. Visit: `https://www.globalharmonyinitiative.com/admin/`
2. Login:
   - Username: admin@globalharmonyinitiative.com
   - Password: GHI@admin2025
3. Check:
   - ✅ Dashboard loads
   - ✅ Tables display data

---

## Step 5: Clear Cache

### **Browser Cache:**
1. Press `Ctrl + Shift + R` (hard refresh)
2. Or: `Ctrl + F5`
3. View in incognito/private mode for fresh perspective

### **Server Cache (Optional):**
If you have caching plugins or .htaccess caching:
1. cPanel → **"File Manager"**
2. Navigate to `cache/` directory
3. Delete all files inside (if any)

---

## Troubleshooting

### **Issue: Files in Wrong Directory**
**Solution**: Use File Manager's "Move" function to relocate files to `public_html` root

### **Issue: Permissions Error**
**Solution**: Set folder permissions to 755, file permissions to 644

### **Issue: .htaccess Not Working**
**Solution**: 
1. Check if .htaccess is visible (enable "Show Hidden Files" in File Manager settings)
2. Verify .htaccess is in the root directory
3. Check that it's the production version (not development)

### **Issue: Images Not Loading**
**Solution**: 
1. Verify `Banners-and-portraits/` and `Logo/` directories uploaded
2. Check file paths in browser console
3. Verify permissions on image directories (755)

### **Issue: Database Connection Error**
**Solution**: 
1. Environment auto-detection should work automatically
2. If not, verify `config/environment.php` uploaded
3. Check database credentials match production

---

## Time Comparison

| Method | Upload Time | Extraction Time | Total Time |
|--------|-------------|-----------------|------------|
| **FTP** | 10-20 min | N/A | **10-20 min** |
| **cPanel Zip** | 1-3 min | 30 seconds | **2-5 min** |

**cPanel Zip is 4-5x FASTER!** ⚡

---

## Benefits of cPanel Zip Method

✅ **Much faster** - Upload one file instead of thousands  
✅ **More reliable** - No connection drops mid-upload  
✅ **Preserves permissions** - File permissions maintained  
✅ **Easier to manage** - One-click extraction  
✅ **Resume capability** - Can retry if upload fails  
✅ **Bandwidth efficient** - Compressed transfer  

---

## Quick Checklist

```
☐ Create GHI-production.zip (30-50 MB)
☐ Login to cPanel
☐ Navigate to public_html
☐ Upload zip file (1-3 min)
☐ Extract zip file
☐ Move files to root (if needed)
☐ Delete zip file
☐ Create: uploads/, logs/, cache/ folders
☐ Set permissions (755)
☐ Test: https://www.globalharmonyinitiative.com
☐ Test admin: .../admin/
☐ Clear browser cache (Ctrl+Shift+R)
```

---

## 🎉 Done!

Your website should now be live and ready for the client demo!

**Total Time**: ~5 minutes (vs 20 minutes via FTP)

---

## Next Steps After Demo

After client approval:
1. Fix admin dashboard modals
2. Apply any requested changes
3. Final testing
4. Official launch 🚀

