# Refactoring & Optimization Plan
## Global Harmony Initiative Admin Dashboard

## Table of Contents
1. [Code Refactoring Opportunities](#code-refactoring-opportunities)
2. [Composer Package Recommendations](#composer-package-recommendations)
3. [Inline Code Migration](#inline-code-migration)
4. [Asset Bundling & Minification](#asset-bundling--minification)
5. [Implementation Plan](#implementation-plan)

---

## 1. Code Refactoring Opportunities

### 1.1 View Pages Component Extraction

**Issue:** All view pages (`cause-view.php`, `event-view.php`, `initiative-view.php`, `story-view.php`, `impact-view.php`) share 90% identical structure.

**Solution:** Create reusable view component system.

**Files to Create:**
- `admin/includes/view-page-header.php` - Header with breadcrumbs and action buttons
- `admin/includes/view-page-content.php` - Main content area template
- `admin/includes/view-page-sidebar.php` - Status and actions sidebar
- `admin/includes/view-field-row.php` - Reusable field display row

**Benefits:**
- Single source of truth for view page layout
- Easier maintenance and updates
- Consistent styling across all views
- Reduced code duplication (~70% reduction)

### 1.2 Edit Pages Component Extraction

**Issue:** Edit pages share similar form structures and validation patterns.

**Solution:** Create form component library.

**Files to Create:**
- `admin/includes/form-field.php` - Reusable form field component
- `admin/includes/form-image-upload.php` - Image upload component
- `admin/includes/form-actions.php` - Form action buttons component

### 1.3 Filter Form Component

**Issue:** Filter forms are duplicated across list pages with only minor variations.

**Solution:** Create reusable filter component.

**File to Create:**
- `admin/includes/filter-form.php` - Generic filter form component

**Parameters:**
- Search field placeholder
- Filter options (status, category, etc.)
- Sort options
- Form ID for auto-submit

### 1.4 Delete Confirmation Handler

**Issue:** Inline `onclick="return confirm(...)"` handlers in all view pages.

**Solution:** Move to JavaScript event delegation.

**Implementation:**
- Add `data-delete-confirm` attribute
- Handle in `admin/js/admin.js` (already partially implemented)

### 1.5 Print Functionality

**Issue:** Inline `onclick="window.print()"` in all view pages.

**Solution:** Add CSS class-based handler.

**Implementation:**
- Add `.print-trigger` class
- Handle click in JavaScript

### 1.6 Common Page Initialization

**Issue:** Repeated session start, authentication check, and model loading in every page.

**Solution:** Create base controller/trait.

**File to Create:**
- `admin/includes/page-base.php` - Base page initialization
- Or use a trait: `src/Traits/AdminPageTrait.php`

---

## 2. Composer Package Recommendations

### 2.1 Current Custom Implementations Analysis

#### ✅ Already Using Packages:
- **CSRF Protection:** `symfony/security-csrf` ✓
- **Validation:** `symfony/validator` ✓
- **Rate Limiting:** `symfony/rate-limiter` ✓
- **Caching:** `symfony/cache` ✓
- **File System:** `league/flysystem` ✓
- **CSV:** `league/csv` ✓
- **PDF:** `dompdf/dompdf` ✓
- **Image Processing:** `intervention/image` ✓
- **Auth:** `delight-im/auth` ✓
- **Database:** `doctrine/dbal` ✓

#### 🔄 Could Be Improved:

**2.1.1 Form Builder**
- **Current:** Manual form HTML in each edit page
- **Recommendation:** `symfony/form` + `symfony/form-bundle`
- **Benefits:** 
  - Type-safe form definitions
  - Built-in validation integration
  - CSRF token handling
  - Form theme customization
- **Migration Impact:** Medium (requires form refactoring)

**2.1.2 Template Engine**
- **Current:** Plain PHP templates with includes
- **Recommendation:** Already have `twig/twig` but not fully utilized
- **Benefits:**
  - Better separation of concerns
  - Template inheritance
  - Built-in escaping
  - Better maintainability
- **Migration Impact:** High (requires template conversion)

**2.1.3 HTTP Client**
- **Current:** `guzzlehttp/guzzle` ✓ (already using)
- **Status:** Good

**2.1.4 Slug Generation**
- **Current:** Custom `generateSlug()` function
- **Recommendation:** `cocur/slugify`
- **Benefits:**
  - Better Unicode handling
  - Language-specific rules
  - More reliable
- **Migration Impact:** Low

**2.1.5 Date/Time Handling**
- **Current:** Native PHP `DateTime` and `date()`
- **Recommendation:** `nesbot/carbon` (or keep native if minimal usage)
- **Benefits:**
  - More readable API
  - Better timezone handling
  - Relative time formatting
- **Migration Impact:** Low-Medium

**2.1.6 Query Builder**
- **Current:** Doctrine DBAL QueryBuilder (manual)
- **Recommendation:** Consider `doctrine/orm` for complex relationships
- **Status:** Current implementation is fine for simple CRUD

---

## 3. Inline Code Migration

### 3.1 Inline JavaScript

**Found Inline Scripts:**
1. `admin/api/event-form.php` - Line 155: `<script>` tag
2. `admin/api/story-form.php` - Line 147: `<script>` tag
3. All view pages: `onclick="return confirm(...)"` handlers
4. All view pages: `onclick="window.print()"` handlers

**Migration Plan:**

#### 3.1.1 Delete Confirmation Handlers
**Current:**
```php
<a href="..." onclick="return confirm('Are you sure?');">Delete</a>
```

**Migrate to:**
```php
<a href="..." data-delete-confirm="Are you sure?">Delete</a>
```

**JavaScript:** Already partially implemented in `admin/js/admin.js` - needs enhancement.

#### 3.1.2 Print Handlers
**Current:**
```php
<button onclick="window.print()">Print</button>
```

**Migrate to:**
```php
<button class="print-trigger">Print</button>
```

**JavaScript:** Add to `admin/js/admin.js`

#### 3.1.3 API Form Scripts
**Files:** `admin/api/event-form.php`, `admin/api/story-form.php`

**Action:** Move initialization scripts to `admin/js/form-handler.js` or remove if redundant.

### 3.2 Inline Styles

**Status:** ✅ No inline styles found in admin pages (good!)

**Note:** All styles are in `admin/css/admin.css`

---

## 4. Asset Bundling & Minification

### 4.1 Current Setup

**Build Tool:** Vite ✓
**Configuration:** `vite.config.js`
**Current Output:** Development and production builds

### 4.2 Recommendations

#### 4.2.1 Production Build Optimization

**Current:** Vite handles minification automatically in production mode.

**Enhancements:**
1. Enable CSS minification
2. Enable tree-shaking for unused code
3. Add source maps for production debugging
4. Configure chunk splitting for better caching

#### 4.2.2 Bundle Analysis

**Recommendation:** Add bundle analyzer to identify large dependencies.

**Package:** `rollup-plugin-visualizer`

#### 4.2.3 Code Splitting

**Current:** All admin JS in one bundle (`admin.js`)

**Recommendation:** Split into:
- `admin-core.js` - Essential initialization
- `admin-tables.js` - Table functionality
- `admin-forms.js` - Form handling
- `admin-charts.js` - Chart initialization

**Benefits:**
- Faster initial load
- Better caching
- Load features on demand

---

## 5. Implementation Plan

### Phase 1: Quick Wins (1-2 days)
1. ✅ Migrate inline onclick handlers to data attributes
2. ✅ Create print handler in JavaScript
3. ✅ Enhance delete confirmation handler
4. ✅ Remove inline scripts from API form files

### Phase 2: Component Extraction (3-5 days)
1. Create view page components
2. Refactor all view pages to use components
3. Create form field components
4. Create filter form component
5. Extract common page initialization

### Phase 3: Package Integration (2-3 days)
1. Install `cocur/slugify` and migrate slug generation
2. Evaluate and potentially integrate `symfony/form`
3. Consider `nesbot/carbon` for date handling

### Phase 4: Asset Optimization (1-2 days)
1. Configure Vite for production optimization
2. Implement code splitting
3. Add bundle analysis
4. Test production build

### Phase 5: Template Engine Migration (Optional, 5-7 days)
1. Convert PHP templates to Twig
2. Set up Twig configuration
3. Migrate includes to Twig extends/blocks
4. Test all pages

---

## 6. Priority Recommendations

### High Priority (Do First)
1. **Migrate inline onclick handlers** - Quick win, improves maintainability
2. **Create view page components** - Reduces 70% code duplication
3. **Install cocur/slugify** - Better slug generation, low risk

### Medium Priority
1. **Create form components** - Improves consistency
2. **Code splitting** - Better performance
3. **Bundle optimization** - Faster load times

### Low Priority (Nice to Have)
1. **Symfony Form integration** - Requires significant refactoring
2. **Twig template migration** - Major undertaking, current PHP templates work fine
3. **Carbon date library** - Current DateTime usage is sufficient

---

## 7. Files to Create/Modify

### New Files to Create:
1. `admin/includes/view-page-header.php`
2. `admin/includes/view-page-content.php`
3. `admin/includes/view-page-sidebar.php`
4. `admin/includes/view-field-row.php`
5. `admin/includes/form-field.php`
6. `admin/includes/form-image-upload.php`
7. `admin/includes/form-actions.php`
8. `admin/includes/filter-form.php`
9. `admin/includes/page-base.php` (or trait)

### Files to Modify:
1. All `*-view.php` files - Use components
2. All `*-edit.php` files - Use form components
3. All list pages (`causes.php`, `events.php`, etc.) - Use filter component
4. `admin/js/admin.js` - Add print handler, enhance delete handler
5. `admin/api/*-form.php` - Remove inline scripts
6. `vite.config.js` - Optimize production build

---

## 8. Estimated Impact

### Code Reduction:
- View pages: ~70% reduction (from ~150 lines to ~50 lines each)
- Edit pages: ~40% reduction (form components)
- Filter forms: ~60% reduction (reusable component)

### Performance:
- Initial page load: 20-30% faster (code splitting)
- Bundle size: 15-25% smaller (tree-shaking)
- Maintainability: Significantly improved

### Maintainability:
- Single source of truth for components
- Easier to update styling/behavior
- Consistent user experience
- Reduced bug surface area

---

## Next Steps

1. Review and approve this plan
2. Start with Phase 1 (Quick Wins)
3. Progressively implement phases
4. Test thoroughly after each phase
5. Document component usage
