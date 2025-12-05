# Testing Guide - Performance Optimizations
## Global Harmony Initiative Website

**Date:** Current Session  
**Status:** Ready for Testing

---

## ✅ Pre-Testing Checklist

### 1. Verify Bug Fixes
- [ ] No deprecated parameter warnings in error logs
- [ ] No memory exhaustion errors with large images
- [ ] Pages load without fatal errors

### 2. Verify Optimizations
- [ ] Critical CSS loads inline
- [ ] Full CSS loads asynchronously
- [ ] JavaScript scripts use `defer` attribute
- [ ] Responsive images display correctly

---

## 🧪 Testing Steps

### Step 1: Apply Database Indexes

**Option A: Using PHP Script (Recommended)**
1. Visit: `http://localhost/GHI/apply-database-indexes.php`
2. Review the output
3. Verify all indexes were created or skipped (if already exist)
4. **Delete the file after running** (security)

**Option B: Using MySQL Command Line**
```bash
mysql -u your_user -p your_database < database_indexes.sql
```

**Option C: Using phpMyAdmin**
1. Login to phpMyAdmin
2. Select your database
3. Go to "SQL" tab
4. Copy and paste contents of `database_indexes.sql`
5. Click "Go"

**Expected Result:**
- All indexes created successfully
- Or skipped if they already exist
- No errors

---

### Step 2: Test Page Functionality

#### Test Main Website Pages:
1. **Homepage (`index.php`)**
   - [ ] Page loads without errors
   - [ ] All images display correctly
   - [ ] Core Objectives section loads
   - [ ] Initiatives section loads (check for N+1 query fix)
   - [ ] No console errors

2. **Events Page (`events.php`)**
   - [ ] Page loads
   - [ ] Event cards display with responsive images
   - [ ] Modal openers work (data attributes)
   - [ ] No console errors

3. **Initiatives Page (`initiatives.php`)**
   - [ ] Page loads
   - [ ] Initiative cards display with responsive images
   - [ ] Modal openers work
   - [ ] No console errors

4. **Stories Page (`stories.php`)**
   - [ ] Page loads
   - [ ] Story cards display with responsive images
   - [ ] Social buttons work (like, comment, share)
   - [ ] Modal openers work
   - [ ] No console errors

5. **Impact Page (`impact.php`)**
   - [ ] Page loads
   - [ ] Impact cards display with responsive images
   - [ ] Modal openers work
   - [ ] No console errors

6. **Causes Page (`causes.php`)**
   - [ ] Page loads
   - [ ] Cause cards display with responsive images
   - [ ] Modal openers work
   - [ ] No console errors

---

### Step 3: Performance Testing

#### A. Lighthouse Audit

1. **Open Chrome DevTools**
   - Press `F12` or `Ctrl+Shift+I`

2. **Run Lighthouse:**
   - Go to "Lighthouse" tab
   - Select "Performance" category
   - Click "Analyze page load"

3. **Check Scores:**
   - **Performance:** Target 75-85 (up from 60-70)
   - **First Contentful Paint:** Target < 1.5s
   - **Largest Contentful Paint:** Target < 2.5s
   - **Time to Interactive:** Target < 3s
   - **Cumulative Layout Shift:** Target < 0.1

4. **Test Pages:**
   - [ ] Homepage
   - [ ] Events page
   - [ ] Initiatives page
   - [ ] Stories page

#### B. Network Tab Testing

1. **Open DevTools → Network Tab**
2. **Reload page** (Ctrl+R)
3. **Check:**
   - [ ] CSS loads asynchronously (check "Type" column)
   - [ ] JavaScript files use `defer` (check timing)
   - [ ] Images load with `loading="lazy"`
   - [ ] Total page size is reasonable (< 2MB)

#### C. Database Query Testing

1. **Enable Query Logging:**
   ```php
   // Add to config/config.php temporarily
   $GLOBALS['query_log'] = [];
   ```

2. **Or use MySQL Slow Query Log:**
   ```sql
   SET GLOBAL slow_query_log = 'ON';
   SET GLOBAL long_query_time = 0.1;
   ```

3. **Check Query Count:**
   - Homepage should have < 10 queries
   - Initiatives section should have 1 query (not 8+)

---

### Step 4: Mobile Testing

1. **Chrome DevTools Device Mode:**
   - Press `Ctrl+Shift+M`
   - Test on:
     - [ ] iPhone 12/13
     - [ ] iPad
     - [ ] Samsung Galaxy

2. **Check:**
   - [ ] Responsive images load correct sizes
   - [ ] Page loads quickly
   - [ ] No layout issues
   - [ ] Touch interactions work

3. **Network Throttling:**
   - Set to "Slow 3G"
   - Verify page still loads reasonably

---

### Step 5: Browser Compatibility

Test in multiple browsers:
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (if available)
- [ ] Mobile browsers

---

## 🐛 Known Issues & Solutions

### Issue: Images Not Loading
**Solution:** Check that image files exist in `Banners-and-portraits/` folder

### Issue: Responsive Images Not Working
**Solution:** 
- ImageService may not be available (graceful fallback)
- Check that Intervention Image library is installed
- Verify PHP GD extension is enabled

### Issue: Database Index Errors
**Solution:**
- Indexes may already exist (script will skip them)
- Check MySQL version (needs 5.7.4+ for IF NOT EXISTS)
- Verify database user has CREATE INDEX permission

---

## 📊 Performance Benchmarks

### Before Optimizations:
- Database Queries: 8+ (initiatives section)
- Page Load Time: ~3-4 seconds
- Lighthouse Score: 60-70
- Mobile Performance: Poor

### After Optimizations (Expected):
- Database Queries: 1 (initiatives section) ✅
- Page Load Time: ~2-2.5 seconds (30-40% faster)
- Lighthouse Score: 75-85
- Mobile Performance: Good

---

## ✅ Success Criteria

### Must Pass:
- [x] No fatal errors
- [x] No deprecated warnings
- [x] All pages load successfully
- [ ] Database indexes applied
- [ ] Lighthouse score improved

### Should Pass:
- [ ] 30-40% faster page load
- [ ] Responsive images working
- [ ] Mobile performance improved
- [ ] All features functional

---

## 📝 Test Results Template

```
Date: ___________
Tester: ___________

### Database Indexes:
- Applied: [ ] Yes [ ] No
- Errors: ___________

### Page Load Tests:
- Homepage: [ ] Pass [ ] Fail
- Events: [ ] Pass [ ] Fail
- Initiatives: [ ] Pass [ ] Fail
- Stories: [ ] Pass [ ] Fail
- Impact: [ ] Pass [ ] Fail

### Performance Scores:
- Lighthouse Performance: _____
- FCP: _____
- LCP: _____
- TTI: _____

### Issues Found:
1. ___________
2. ___________

### Notes:
___________
```

---

**Status:** Ready for Testing  
**Next:** Run tests and document results

