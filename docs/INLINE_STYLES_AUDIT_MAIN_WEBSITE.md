# Inline Styles Audit - Main Website Pages

## Summary
Found **70+ inline style attributes** across 9 main website files that should be migrated to `css/style.css` for better maintainability and npm build management.

---

## Files with Inline Styles

### 1. `index.php` - Homepage (46 instances)

#### Carousel Images
**Pattern**: Repeated 3 times
```php
style="object-fit: cover; width: 100%; height: 100vh; overflow: hidden;"
```
**Usage**: Hero carousel images
**Suggested Class**: `.hero-carousel-img`

#### Carousel Captions
**Pattern**: Repeated 3 times
```php
style="max-width: 900px;"
```
**Usage**: Text overlay containers
**Suggested Class**: `.hero-caption-container`

#### About Section Image
```php
style="object-fit: cover; overflow: hidden;"
```
**Usage**: About section feature image
**Suggested Class**: `.about-feature-img`

#### Tab Navigation Spans
**Pattern**: Repeated 3 times
```php
style="min-width: 100px; width: 100%; max-width: 150px; padding: 0 10px;"
```
**Usage**: About tabs (About/Mission/Vision)
**Suggested Class**: `.about-tab-label`

#### Quote Banner Background
```php
style="background: linear-gradient(rgba(0, 6, 86, 0.3), rgba(0, 6, 86, 0.3)), url(...) center center; background-size: cover; position: relative; overflow: hidden;"
```
**Usage**: Parallax quote section
**Suggested Class**: `.quote-banner-bg`

#### Quote Container
```php
style="max-width: 800px;"
```
**Usage**: Centered quote text
**Suggested Class**: `.quote-container`

#### Quote Citation
```php
style="color: var(--ghi-secondary);"
```
**Usage**: Quote author styling
**Suggested Class**: `.quote-citation`

#### Foundation Section Background
```php
style="background: var(--ghi-accent-3);"
```
**Usage**: Section background color
**Suggested Class**: `.foundation-section-bg`

#### Foundation Cards (Mission/Vision)
**Pattern**: Repeated 2 times
```php
style="background: linear-gradient(135deg, var(--ghi-primary) 0%, var(--ghi-accent-5) 100%);"
```
**Usage**: Gradient card backgrounds
**Suggested Class**: `.foundation-card-gradient`

#### Foundation Icons
**Pattern**: Repeated 3 times (Mission, Vision, Values)
```php
style="font-size: 2rem;"
```
**Usage**: Large icons in cards
**Suggested Class**: `.foundation-icon`

#### Value Descriptions
```php
style="color: var(--ghi-primary);"
```
**Usage**: Text color for values
**Suggested Class**: `.value-description`

#### Section Headers
**Pattern**: Repeated 4 times
```php
style="max-width: 800px;"
```
**Usage**: Centered section headers
**Suggested Class**: `.section-header-container`

#### Impact Images
```php
style="object-fit: cover; overflow: hidden;"
```
**Usage**: Impact activity images
**Suggested Class**: `.impact-card-img`

#### Counter Section Background
```php
style="background: linear-gradient(rgba(0, 0, 0, .4), rgba(0, 0, 0, 0.4)), url(...) center center; background-size: cover; overflow: hidden;"
```
**Usage**: Stats counter parallax background
**Suggested Class**: `.counter-section-bg`

#### Counter Numbers
**Pattern**: Repeated 4 times
```php
style="font-size: 30px;"
```
**Usage**: Counter display size
**Suggested Class**: `.counter-display`

---

### 2. `initiatives.php` (2 instances)

#### Progress Bar Container
```php
style="height: 8px; background: rgba(255, 255, 255, 0.3);"
```
**Usage**: Progress track
**Suggested Class**: `.initiative-progress-track`

#### Progress Bar Fill
```php
style="width: <?php echo $progress; ?>%"
```
**Usage**: Dynamic progress width (Keep as inline - dynamic value)
**Action**: ✅ Keep inline (dynamic PHP value)

---

### 3. `events.php` (1 instance)

#### Status Badge
```php
style="background: <?php echo $event['status'] === 'upcoming' ? '#28a745' : (...); ?> !important; color: white !important;"
```
**Usage**: Dynamic status color
**Action**: Move to CSS classes: `.badge-upcoming`, `.badge-ongoing`, `.badge-completed`

---

### 4. `stories.php` (4 instances)

#### Category Badge Position
```php
style="left: auto; right: 10px;"
```
**Usage**: Right-aligned badge
**Suggested Class**: `.badge-right`

#### Social Action Buttons
**Pattern**: Repeated 3 times (Like, Comment, Share)
```php
style="background: rgba(255, 255, 255, 0.2); color: white; border: 1px solid white;"
```
**Usage**: Semi-transparent social buttons
**Suggested Class**: `.story-social-btn`

---

### 5. `impact.php` (1 instance)

#### Objective Badge Position
```php
style="left: auto; right: 10px;"
```
**Usage**: Right-aligned badge
**Suggested Class**: `.badge-right` (same as stories)

---

### 6. `includes/header.php` (2 instances)

#### Logo Image
```php
style="max-height: 60px;"
```
**Usage**: Navbar logo sizing
**Suggested Class**: `.navbar-logo-img`

#### Donate Button Container
```php
style="margin-left: 15px;"
```
**Usage**: Button spacing
**Suggested Class**: `.navbar-cta-container`

---

### 7. `includes/footer.php` (5 instances)

#### Footer Background
```php
style="background: var(--ghi-accent-3);"
```
**Usage**: Footer section background
**Suggested Class**: `.footer-bg`

#### Footer Text
**Pattern**: Repeated 4 times
```php
style="color: var(--ghi-primary);"
```
**Usage**: Text color throughout footer
**Suggested Class**: `.footer-text`

---

### 8. `includes/sidebar.php` (2 instances)

#### Breadcrumb Links
```php
style="color: black;"
```
**Usage**: Breadcrumb styling
**Suggested Class**: `.breadcrumb-link`, `.breadcrumb-item-custom`

---

### 9. `404.php` (7 instances)

#### Hero Section Background
```php
style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 120px 0 80px;"
```
**Usage**: Error page hero
**Suggested Class**: `.error-hero-bg`

#### Error Code
```php
style="font-size: 120px; font-weight: bold; opacity: 0.3; margin-bottom: 20px;"
```
**Usage**: Large "404" text
**Suggested Class**: `.error-code`

#### Lead Text
```php
style="max-width: 600px; margin: 0 auto;"
```
**Usage**: Centered description
**Suggested Class**: `.error-lead-text`

#### Icon Circles
**Pattern**: Repeated 4 times (different gradients)
```php
style="width: 60px; height: 60px; background: linear-gradient(...); border-radius: 50%; display: flex; align-items: center; justify-content: center;"
```
**Usage**: Circular icon containers
**Suggested Class**: `.icon-circle-gradient-1`, `.icon-circle-gradient-2`, etc.

---

### 10. `coming-soon-donate.php` (9 instances)

#### Hero Section
```php
style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 120px 0 80px; position: relative; overflow: hidden;"
```
**Usage**: Coming soon hero
**Suggested Class**: `.coming-soon-hero-bg`

#### Container Z-Index
```php
style="z-index: 2;"
```
**Usage**: Content layering
**Suggested Class**: `.hero-content-layer`

#### Heart Icon
```php
style="font-size: 100px; opacity: 0.9;"
```
**Usage**: Large decorative icon
**Suggested Class**: `.coming-soon-icon-large`

#### Lead Titles
**Pattern**: Repeated 2 times
```php
style="font-size: 2rem; font-weight: 300;"
style="max-width: 700px; margin: 0 auto;"
```
**Usage**: Subtitle styling
**Suggested Class**: `.coming-soon-subtitle`, `.coming-soon-description`

#### Feature Icon Circles
**Pattern**: Repeated 4 times (different gradients)
```php
style="width: 70px; height: 70px; background: linear-gradient(...); border-radius: 50%; display: flex; align-items: center; justify-content: center;"
```
**Usage**: Feature card icons
**Suggested Class**: `.feature-icon-circle-1`, `.feature-icon-circle-2`, etc.

---

## Migration Strategy

### Phase 1: Common Patterns (High Impact)
1. **Container Max-Widths** - Create utility classes
   - `.max-w-600`, `.max-w-700`, `.max-w-800`, `.max-w-900`

2. **Icon Circles** - Create base class with variants
   - `.icon-circle` (base)
   - `.icon-circle-lg`, `.icon-circle-sm`
   - `.icon-circle-gradient-{1-4}` (color variants)

3. **Background Gradients** - CSS custom properties
   - `--gradient-hero`
   - `--gradient-foundation`
   - `--gradient-error`

4. **Object-fit Images** - Utility classes
   - `.img-cover-full` (cover + 100vh)
   - `.img-cover-auto` (cover + auto height)

### Phase 2: Component-Specific Styles
1. **Hero/Carousel Components**
2. **Card Components** (initiatives, events, stories)
3. **Badge Components** (status, category, region)
4. **Social Buttons**
5. **Progress Bars**

### Phase 3: Layout/Utility Classes
1. **Text Colors** (footer, values, etc.)
2. **Backgrounds** (sections)
3. **Spacing** (margins, padding)
4. **Typography** (font sizes, weights)

---

## Recommended CSS File Structure

```css
/* ===== UTILITY CLASSES ===== */
/* Max Width Containers */
.max-w-600 { max-width: 600px; margin: 0 auto; }
.max-w-700 { max-width: 700px; margin: 0 auto; }
.max-w-800 { max-width: 800px; margin: 0 auto; }
.max-w-900 { max-width: 900px; margin: 0 auto; }

/* Icon Circles */
.icon-circle { ... }
.icon-circle-lg { width: 70px; height: 70px; }
.icon-circle-sm { width: 60px; height: 60px; }

/* ===== HERO SECTIONS ===== */
.hero-carousel-img { object-fit: cover; width: 100%; height: 100vh; overflow: hidden; }
.hero-caption-container { max-width: 900px; }
.error-hero-bg { ... }
.coming-soon-hero-bg { ... }

/* ===== CARDS ===== */
.foundation-card-gradient { ... }
.impact-card-img { ... }

/* ===== BADGES ===== */
.badge-right { left: auto; right: 10px; }
.badge-upcoming { background: #28a745 !important; color: white !important; }
.badge-ongoing { background: #ffc107 !important; color: white !important; }
.badge-completed { background: #6c757d !important; color: white !important; }

/* ===== SOCIAL BUTTONS ===== */
.story-social-btn { ... }

/* ===== PROGRESS BARS ===== */
.initiative-progress-track { height: 8px; background: rgba(255, 255, 255, 0.3); }

/* ===== SECTIONS ===== */
.quote-banner-bg { ... }
.counter-section-bg { ... }
.foundation-section-bg { background: var(--ghi-accent-3); }
.footer-bg { background: var(--ghi-accent-3); }

/* ===== TYPOGRAPHY ===== */
.error-code { font-size: 120px; font-weight: bold; opacity: 0.3; margin-bottom: 20px; }
.counter-display { font-size: 30px; }
.foundation-icon { font-size: 2rem; }
.coming-soon-icon-large { font-size: 100px; opacity: 0.9; }

/* ===== FOOTER ===== */
.footer-text { color: var(--ghi-primary); }
.navbar-logo-img { max-height: 60px; }
```

---

## Benefits of Migration

1. **Consistency** - All similar elements styled the same way
2. **Maintainability** - One place to update styles
3. **Performance** - Cached CSS, smaller HTML
4. **Reusability** - Classes can be reused across pages
5. **Build Tool Integration** - npm can process all styles
6. **Developer Experience** - Better IDE support, linting

---

## Priority Ranking

### HIGH PRIORITY (Most Common/Repeated)
- ✅ Max-width containers (repeated 10+ times)
- ✅ Icon circles (repeated 8+ times)
- ✅ Background gradients (repeated 6+ times)
- ✅ Object-fit images (repeated 5+ times)

### MEDIUM PRIORITY (Semantic/Reusable)
- ⚠️ Badge variants
- ⚠️ Social buttons
- ⚠️ Card components
- ⚠️ Section backgrounds

### LOW PRIORITY (Single Use/Minor)
- ⚡ Breadcrumb styling
- ⚡ Navbar spacing
- ⚡ Footer text colors

---

## Exceptions (Keep as Inline)

These should remain inline due to dynamic PHP values:
- Progress bar widths (calculated from database)
- Any conditional styles based on PHP variables

---

## Estimated Impact

**Total Inline Styles**: 70+
**Potential CSS Classes**: ~40-50
**Estimated LOC Reduction**: ~150-200 lines of inline styles
**Affected Files**: 9 main website files
**Estimated Time**: 3-4 hours for full migration

---

**Status**: 📋 Audit Complete - Ready for Migration
**Next Step**: Create CSS classes in `css/style.css` and update PHP files

