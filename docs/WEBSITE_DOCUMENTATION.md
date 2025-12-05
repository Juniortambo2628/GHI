# Global Harmony Initiative Website - Comprehensive Documentation

## Table of Contents
1. [Overview](#overview)
2. [Technology Stack](#technology-stack)
3. [Website Structure](#website-structure)
4. [Pages & Features](#pages--features)
5. [Database Structure](#database-structure)
6. [Dynamic Content Rendering](#dynamic-content-rendering)
7. [Components & Reusable Elements](#components--reusable-elements)
8. [Admin Panel](#admin-panel)
9. [API Endpoints](#api-endpoints)
10. [Frontend Assets & Dependencies](#frontend-assets--dependencies)
11. [Configuration](#configuration)
12. [File Upload System](#file-upload-system)
13. [Search Functionality](#search-functionality)
14. [Implementation Notes](#implementation-notes)

---

## Overview

The Global Harmony Initiative (GHI) website is a PHP-based content management system for a U.S.-registered 501(c)(3) nonprofit organization working in East Africa. The website features a public-facing site with dynamic content management and a comprehensive admin dashboard for content administration.

**Key Characteristics:**
- PHP 8.1+ backend with MySQL database
- Responsive design with mobile support
- AJAX-based navigation for smooth page transitions
- Custom loader animation
- Glassmorphism-styled admin dashboard
- Dynamic content rendering from database
- File upload system for images
- Site-wide search functionality

---

## Technology Stack

### Backend
- **PHP**: Version 8.1 or higher
- **MySQL/MariaDB**: Database system
- **PDO**: Database abstraction layer
- **Composer**: Dependency management and autoloading

### Frontend
- **Bootstrap 4**: CSS framework
- **jQuery 3.3.1**: JavaScript library
- **Owl Carousel**: Image/carousel functionality
- **AOS (Animate On Scroll)**: Scroll animations
- **Font Awesome (via Icomoon)**: Icon library
- **Custom CSS**: Multiple stylesheets for different sections

### Development Dependencies
- **Composer**: For autoloading and PSR-4 namespace support
- **PHPUnit**: Testing framework (included in vendor)

---

## Website Structure

### Directory Structure
```
Global-Harmony-Initiative/
├── admin/                    # Admin dashboard
│   ├── includes/            # Admin header/footer
│   ├── login.php            # Admin authentication
│   ├── logout.php           # Admin logout
│   ├── index.php            # Dashboard home
│   ├── causes.php           # Causes management
│   ├── cause-edit.php       # Create/edit causes
│   ├── cause-delete.php     # Delete causes
│   ├── initiatives.php      # Initiatives management
│   ├── initiative-edit.php  # Create/edit initiatives
│   ├── initiative-delete.php# Delete initiatives
│   ├── events.php           # Events management
│   ├── event-edit.php       # Create/edit events
│   ├── event-delete.php     # Delete events
│   ├── impact-activities.php# Impact activities management
│   ├── impact-edit.php      # Create/edit impact activities
│   ├── impact-delete.php    # Delete impact activities
│   ├── contact-submissions.php # Contact form submissions
│   └── newsletter.php        # Newsletter subscribers
├── api/                     # API endpoints
│   ├── contact.php          # Contact form handler
│   ├── donate.php           # Donation form handler
│   ├── volunteer.php        # Volunteer form handler
│   ├── newsletter.php       # Newsletter subscription
│   └── search.php           # Site-wide search API
├── config/                  # Configuration files
│   ├── config.php           # Main configuration
│   └── database.php         # Database class
├── includes/                # Reusable PHP components
│   ├── header.php           # Site header (navigation, meta tags)
│   ├── footer.php           # Site footer (scripts, newsletter)
│   ├── sidebar.php          # Sidebar component (if used)
│   ├── constants.php         # Application constants
│   └── functions.php        # Helper functions
├── css/                     # Stylesheets
│   ├── bootstrap.min.css    # Bootstrap framework
│   ├── style.css            # Main stylesheet
│   ├── admin.css            # Admin dashboard styles
│   ├── loader.css           # Loader animation styles
│   ├── search.css           # Search functionality styles
│   ├── impact.css           # Impact page styles
│   ├── events.css           # Events page styles
│   ├── initiatives.css      # Initiatives page styles
│   └── [other CSS files]
├── js/                      # JavaScript files
│   ├── jquery-3.3.1.min.js  # jQuery library
│   ├── bootstrap.min.js     # Bootstrap JS
│   ├── main.js              # Main JavaScript
│   ├── search.js            # Search functionality
│   └── [other JS libraries]
├── images/                  # Static images
├── uploads/                 # User-uploaded files (writable)
├── Logo/                    # Logo files
├── fonts/                   # Custom fonts and icons
├── loader-icon/             # Loader SVG files
├── vendor/                  # Composer dependencies
├── index.php                # Home page
├── about.php                # About Us page
├── causes.php               # Our Causes page
├── initiatives.php          # Our Initiatives page
├── events.php               # Events & Activities page
├── impact.php               # Our Impact page
├── blog.php                 # Blog page (placeholder)
├── get-involved.php         # Get Involved page
├── contact.php              # Contact Us page
├── setup-database.php       # Database setup script
├── composer.json            # Composer configuration
└── composer.lock            # Composer lock file
```

---

## Pages & Features

### 1. Home Page (`index.php`)

**Features:**
- Hero carousel with 3 rotating images
- Core Objectives section (5 objectives displayed in grid)
- About Us section with tabbed interface:
  - Who We Are
  - Mission & Vision
  - Our Values (5 core values with icons)
  - Our Approach (4 approaches)
- Inspirational quote banner
- Donation form section
- Call-to-action section

**Dynamic Content:**
- Objectives loaded from `OBJECTIVES` constant in `includes/constants.php`
- Core values loaded from `CORE_VALUES` constant
- Images cycle through `img_1.jpg`, `img_2.jpg`, `img_3.jpg` for objectives

**Static Content:**
- Hero images: `hero_1.jpg`, `hero_2.jpg`, `hero_3.jpg`
- Mission, vision, and approach text (hardcoded)

---

### 2. About Us Page (`about.php`)

**Features:**
- Hero banner with page title
- Who We Are section with image
- Mission & Vision section (two-column layout)
- Core Values display (5 values with icons)
- Our Approach section (4 approaches with numbered items)
- Link to Impact page

**Dynamic Content:**
- Core values loaded from `CORE_VALUES` constant
- Values icons mapped: heart, shield, users, hand-up, leaf

**Static Content:**
- Organization description
- Mission and vision statements
- Approach descriptions

---

### 3. Our Causes Page (`causes.php`)

**Features:**
- Hero banner
- List of all active causes from database
- Each cause displays:
  - Image (from uploads or fallback)
  - Title and description
  - Quote (if available)
  - Related initiatives (up to 3)
  - Link to view all initiatives for that cause
  - "Get Involved" button

**Dynamic Content:**
- Causes fetched from `causes` table (status = 'active')
- Ordered by `display_order` then `title`
- Related initiatives fetched for each cause
- Images from `/uploads/` directory or fallback to `img_1.jpg`, `img_2.jpg`, `img_3.jpg`

**Database Queries:**
```sql
SELECT * FROM causes WHERE status = 'active' ORDER BY display_order ASC, title ASC
SELECT * FROM initiatives WHERE cause_id = ? AND status = 'published' ORDER BY created_at DESC LIMIT 3
```

---

### 4. Our Initiatives Page (`initiatives.php`)

**Features:**
- Hero banner
- Two display modes:
  - **List View**: Grid of all initiatives
  - **Detail View**: Single initiative with related events

**List View:**
- Grid of initiative cards
- Each card shows:
  - Image (if available)
  - Cause category
  - Title (linked)
  - Description (truncated)
  - "View Events & Activities" button

**Detail View (when `?initiative=ID`):**
- Full initiative details
- Breadcrumb navigation (Causes → Initiative)
- Related events & activities
- Event cards with:
  - Image or logo placeholder
  - Upcoming/Past badge
  - Date and location
  - Description
  - Link to event detail page

**Dynamic Content:**
- Initiatives from `initiatives` table (status = 'published')
- Can be filtered by cause (`?cause=ID`)
- Events fetched for selected initiative
- Event status determined by comparing `event_date` with current date

**Database Queries:**
```sql
-- List initiatives
SELECT i.*, c.title as cause_title FROM initiatives i 
LEFT JOIN causes c ON i.cause_id = c.id 
WHERE i.status = 'published' ORDER BY i.created_at DESC

-- Single initiative with events
SELECT i.*, c.title as cause_title FROM initiatives i 
LEFT JOIN causes c ON i.cause_id = c.id 
WHERE i.id = ? AND i.status = 'published'

SELECT * FROM events WHERE initiative_id = ? AND status = 'published' ORDER BY event_date ASC
```

---

### 5. Events & Activities Page (`events.php`)

**Features:**
- Hero banner
- Two display modes:
  - **List View**: Grid of all events
  - **Detail View**: Single event with impact activities

**List View:**
- Grid of event cards
- Each card shows:
  - Image or logo placeholder
  - Upcoming/Past status badge
  - Title
  - Date and time
  - Location
  - Description (truncated)
  - "View Impact" button

**Detail View (when `?event=ID`):**
- Full event details
- Breadcrumb navigation (Causes → Initiative → Event)
- Date and location information
- Impact Activities section:
  - Grid of impact cards
  - Each card shows:
    - Thumbnail image
    - Title
    - Description
    - People affected count
    - Outcome summary
    - Link to related event

**Dynamic Content:**
- Events from `events` table (status = 'published')
- Ordered by `event_date` (upcoming first)
- Event status calculated: `event_date > now()` = upcoming
- Impact activities fetched for selected event
- Total people affected calculated and displayed

**Database Queries:**
```sql
-- List events
SELECT * FROM events WHERE status = 'published' ORDER BY event_date ASC, created_at DESC

-- Single event with impact
SELECT e.*, i.title as initiative_title, c.title as cause_title 
FROM events e 
LEFT JOIN initiatives i ON e.initiative_id = i.id 
LEFT JOIN causes c ON i.cause_id = c.id 
WHERE e.id = ? AND e.status = 'published'

SELECT * FROM impact_activities WHERE event_id = ? AND status = 'published' ORDER BY display_order ASC
```

---

### 6. Our Impact Page (`impact.php`)

**Features:**
- Hero banner
- Total Impact counter (sum of all people affected)
- Grid of impact activity cards
- Each card shows:
  - Thumbnail image or logo placeholder
  - Title
  - Related event link
  - Description
  - People affected count
  - Outcome summary
  - Link to related event

**Dynamic Content:**
- Impact activities from `impact_activities` table (status = 'published')
- Ordered by `display_order` then `created_at`
- Total people affected calculated: `SUM(people_affected)`
- Related event, initiative, and cause information via JOINs

**Database Query:**
```sql
SELECT ia.*, e.title as event_title, e.id as event_id, i.title as initiative_title, c.title as cause_title 
FROM impact_activities ia 
LEFT JOIN events e ON ia.event_id = e.id 
LEFT JOIN initiatives i ON e.initiative_id = i.id 
LEFT JOIN causes c ON i.cause_id = c.id 
WHERE ia.status = 'published' 
ORDER BY ia.display_order ASC, ia.created_at DESC
```

---

### 7. Get Involved Page (`get-involved.php`)

**Features:**
- Hero banner
- Four ways to get involved:
  1. Partner With Us
  2. Donate (with form)
  3. Volunteer (with form)
  4. Advocate
- Donation form (POST to `/api/donate.php`)
- Volunteer application form (POST to `/api/volunteer.php`)
- Call-to-action section

**Forms:**
- **Donation Form**: Name, Email, Amount
- **Volunteer Form**: First Name, Last Name, Email, Phone, Message

**Dynamic Content:**
- Forms submit via AJAX to API endpoints
- Success/error messages displayed to user

---

### 8. Contact Us Page (`contact.php`)

**Features:**
- Hero banner
- Contact form (POST to `/api/contact.php`)
- Contact information sidebar:
  - Email address
  - Phone (US and East Africa)
  - Office addresses (placeholders)
  - Social media links

**Form Fields:**
- First Name, Last Name, Email, Message

**Dynamic Content:**
- Contact info from constants: `SITE_EMAIL`, `SITE_PHONE_US`, `SITE_PHONE_EA`
- Form submission handled by API endpoint

---

### 9. Blog Page (`blog.php`)

**Current Status:** Placeholder page
- "Coming Soon" message
- No database integration yet
- Structure ready for future blog implementation

---

## Database Structure

### Tables

#### 1. `users`
Admin user accounts for the admin panel.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `username` (VARCHAR(100), UNIQUE, NOT NULL)
- `email` (VARCHAR(191), UNIQUE, NOT NULL)
- `password_hash` (VARCHAR(255), NOT NULL)
- `role` (ENUM('admin', 'editor'), DEFAULT 'editor')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Indexes:**
- `unique_username` on `username`
- `unique_email` on `email`

---

#### 2. `causes`
Core objectives/causes that the organization supports.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `title` (VARCHAR(255), NOT NULL)
- `slug` (VARCHAR(191), UNIQUE, NOT NULL)
- `description` (TEXT, NOT NULL)
- `quote` (TEXT, NULL)
- `image` (VARCHAR(255), NULL) - Filename in uploads directory
- `display_order` (INT, DEFAULT 0)
- `status` (ENUM('active', 'inactive'), DEFAULT 'active')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Indexes:**
- `unique_slug` on `slug`
- `idx_display_order` on `display_order`

**Relationships:**
- One-to-many with `initiatives` (via `initiatives.cause_id`)

---

#### 3. `initiatives`
Programs and projects under each cause.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `title` (VARCHAR(255), NOT NULL)
- `slug` (VARCHAR(191), UNIQUE, NOT NULL)
- `description` (TEXT, NULL)
- `content` (LONGTEXT, NULL) - Full HTML content
- `image` (VARCHAR(255), NULL) - Filename in uploads directory
- `category` (ENUM('education', 'health', 'livelihood', 'empowerment', 'partnerships'), NOT NULL)
- `cause_id` (INT, NULL) - Foreign key to `causes.id`
- `status` (ENUM('draft', 'published'), DEFAULT 'draft')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Indexes:**
- `unique_slug` on `slug`
- `idx_cause_id` on `cause_id`

**Foreign Keys:**
- `fk_initiative_cause` → `causes.id` (ON DELETE SET NULL, ON UPDATE CASCADE)

**Relationships:**
- Many-to-one with `causes`
- One-to-many with `events` (via `events.initiative_id`)

---

#### 4. `events`
Events and activities organized by the organization.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `title` (VARCHAR(255), NOT NULL)
- `slug` (VARCHAR(191), UNIQUE, NOT NULL)
- `description` (TEXT, NULL)
- `content` (LONGTEXT, NULL) - Full HTML content
- `image` (VARCHAR(255), NULL) - Filename in uploads directory
- `event_date` (DATETIME, NULL)
- `location` (VARCHAR(255), NULL)
- `initiative_id` (INT, NULL) - Foreign key to `initiatives.id`
- `status` (ENUM('draft', 'published'), DEFAULT 'draft')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Indexes:**
- `unique_slug` on `slug`
- `idx_initiative_id` on `initiative_id`

**Foreign Keys:**
- `fk_event_initiative` → `initiatives.id` (ON DELETE SET NULL, ON UPDATE CASCADE)

**Relationships:**
- Many-to-one with `initiatives`
- One-to-many with `impact_activities` (via `impact_activities.event_id`)

---

#### 5. `impact_activities`
Impact stories and outcomes from events.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `title` (VARCHAR(255), NOT NULL)
- `slug` (VARCHAR(191), UNIQUE, NOT NULL)
- `description` (TEXT, NOT NULL)
- `event_id` (INT, NULL) - Foreign key to `events.id`
- `people_affected` (INT, DEFAULT 0)
- `outcome_summary` (TEXT, NULL)
- `thumbnail` (VARCHAR(255), NULL) - Filename in uploads directory
- `image` (VARCHAR(255), NULL) - Filename in uploads directory
- `status` (ENUM('draft', 'published'), DEFAULT 'published')
- `display_order` (INT, DEFAULT 0)
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- `updated_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)

**Indexes:**
- `unique_slug` on `slug`
- `idx_status` on `status`
- `idx_display_order` on `display_order`
- `idx_event_id` on `event_id`

**Foreign Keys:**
- `fk_impact_event` → `events.id` (ON DELETE SET NULL, ON UPDATE CASCADE)

**Relationships:**
- Many-to-one with `events`

---

#### 6. `contact_submissions`
Contact form submissions.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `name` (VARCHAR(255), NOT NULL)
- `email` (VARCHAR(191), NOT NULL)
- `message` (TEXT, NOT NULL)
- `status` (ENUM('new', 'read', 'replied'), DEFAULT 'new')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

**Usage:**
- Also used for volunteer applications (message field contains application details)

---

#### 7. `newsletter_subscriptions`
Newsletter email subscriptions.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `email` (VARCHAR(191), UNIQUE, NOT NULL)
- `status` (ENUM('active', 'unsubscribed'), DEFAULT 'active')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

**Indexes:**
- `unique_email` on `email`

---

### Database Relationships Diagram

```
causes (1) ──< (many) initiatives (1) ──< (many) events (1) ──< (many) impact_activities
```

---

## Dynamic Content Rendering

### How Dynamic Content Works

1. **Database-Driven Content:**
   - Causes, Initiatives, Events, and Impact Activities are stored in MySQL
   - Content is fetched using PDO prepared statements
   - Status fields control visibility (`active`/`published` vs `inactive`/`draft`)

2. **Constants-Based Content:**
   - Core Objectives (`OBJECTIVES`) defined in `includes/constants.php`
   - Core Values (`CORE_VALUES`) defined in `includes/constants.php`
   - Inspirational Quotes (`INSPIRATIONAL_QUOTES`) defined in `includes/constants.php`

3. **Rendering Flow:**
   ```
   Page Request → Load Config → Load Database → Query Data → Render HTML → Output
   ```

4. **Image Handling:**
   - Images stored in `/uploads/` directory
   - Filenames stored in database
   - Fallback images used if database image is missing
   - Logo placeholder used for events/impact without images

5. **Status-Based Filtering:**
   - Public pages only show `status = 'published'` or `status = 'active'`
   - Admin panel shows all records regardless of status

6. **Ordering:**
   - Causes: `display_order ASC, title ASC`
   - Initiatives: `created_at DESC`
   - Events: `event_date ASC, created_at DESC`
   - Impact Activities: `display_order ASC, created_at DESC`

---

## Components & Reusable Elements

### 1. Header (`includes/header.php`)

**Features:**
- HTML head section with meta tags
- CSS stylesheets (Bootstrap, custom styles)
- JavaScript libraries (jQuery, Bootstrap, etc.)
- Page loader overlay
- Top bar (desktop only) with quick links and social media
- Main navigation menu
- Search box (desktop only)
- Mobile menu toggle
- Active page highlighting

**Dynamic Elements:**
- Page title from `$pageTitle` variable
- Meta description from `$pageDescription` variable
- Active navigation item based on current page
- BASE_URL used for all links

**CSS Files Loaded:**
- Bootstrap, Animate.css, Fancybox, Owl Carousel, AOS
- Custom: style.css, loader.css, search.css, impact.css, events.css, initiatives.css, etc.

---

### 2. Footer (`includes/footer.php`)

**Features:**
- About Us section
- Quick Links
- Newsletter subscription form
- Social media links
- Copyright information
- JavaScript files
- Loader animation script
- AJAX navigation script

**Dynamic Elements:**
- Current year in copyright
- Site name from `SITE_NAME` constant
- Newsletter form submits to `/api/newsletter.php`
- AJAX form handling

**JavaScript Files:**
- jQuery, Popper, Bootstrap, Owl Carousel, Sticky, Waypoints, Animate Number, Fancybox, Easing, AOS, main.js, search.js

---

### 3. Helper Functions (`includes/functions.php`)

**Functions:**
- `e($string)`: HTML escape/sanitize output
- `getCurrentPage()`: Get current page name
- `isActivePage($page)`: Check if page is active for navigation
- `formatDate($date, $format)`: Format date strings
- `generateSlug($string)`: Generate URL-friendly slugs
- `truncate($text, $length, $suffix)`: Truncate text with ellipsis

---

### 4. Constants (`includes/constants.php`)

**Defined Constants:**
- `OBJECTIVES`: Array of 5 core objectives with title, description, quote
- `CORE_VALUES`: Array of 5 values with name, description, icon
- `INSPIRATIONAL_QUOTES`: Array of quotes with quote and author

---

## Admin Panel

### Access
- URL: `/admin/login.php`
- Default credentials:
  - Username: `admin`
  - Password: `admin123` (MUST BE CHANGED)

### Features

#### 1. Dashboard (`admin/index.php`)
- Statistics cards:
  - Causes count
  - Initiatives count
  - Events count
  - Impact Activities count
  - New contact messages count
  - Newsletter subscribers count
- Recent contact submissions table
- Quick links to manage each section

#### 2. Causes Management (`admin/causes.php`)
- List all causes
- Create new cause (`admin/cause-edit.php`)
- Edit existing cause (`admin/cause-edit.php?id=ID`)
- Delete cause (`admin/cause-delete.php?id=ID`)

**Cause Fields:**
- Title
- Slug (auto-generated from title)
- Description
- Quote (optional)
- Image upload
- Display Order
- Status (active/inactive)

#### 3. Initiatives Management (`admin/initiatives.php`)
- List all initiatives
- Create new initiative (`admin/initiative-edit.php`)
- Edit existing initiative (`admin/initiative-edit.php?id=ID`)
- Delete initiative (`admin/initiative-delete.php?id=ID`)

**Initiative Fields:**
- Title
- Slug (auto-generated)
- Description
- Content (HTML/WYSIWYG)
- Image upload
- Category (education, health, livelihood, empowerment, partnerships)
- Cause (dropdown - links to causes)
- Status (draft/published)

#### 4. Events Management (`admin/events.php`)
- List all events
- Create new event (`admin/event-edit.php`)
- Edit existing event (`admin/event-edit.php?id=ID`)
- Delete event (`admin/event-delete.php?id=ID`)

**Event Fields:**
- Title
- Slug (auto-generated)
- Description
- Content (HTML/WYSIWYG)
- Image upload
- Event Date & Time
- Location
- Initiative (dropdown - links to initiatives)
- Status (draft/published)

#### 5. Impact Activities Management (`admin/impact-activities.php`)
- List all impact activities
- Create new impact (`admin/impact-edit.php`)
- Edit existing impact (`admin/impact-edit.php?id=ID`)
- Delete impact (`admin/impact-delete.php?id=ID`)

**Impact Activity Fields:**
- Title
- Slug (auto-generated)
- Description
- Event (dropdown - links to events)
- People Affected (number)
- Outcome Summary
- Thumbnail upload
- Image upload
- Display Order
- Status (draft/published)

#### 6. Contact Submissions (`admin/contact-submissions.php`)
- View all contact form submissions
- Filter by status (new/read/replied)
- Mark as read/replied
- View individual submission details

#### 7. Newsletter Subscribers (`admin/newsletter.php`)
- List all newsletter subscribers
- Filter by status (active/unsubscribed)
- Export functionality (if implemented)

### Admin Styling
- Glassmorphism design (light mode)
- Glass cards with transparency
- Color-coded stat icons
- Responsive tables
- Modern UI with smooth transitions

---

## API Endpoints

All API endpoints return JSON responses and are located in `/api/` directory.

### 1. Contact Form (`/api/contact.php`)

**Method:** POST

**Parameters:**
- `firstname` (required)
- `lastname` (required)
- `email` (required, validated)
- `message` (required)

**Response:**
```json
{
  "success": true,
  "message": "Thank you for your message. We will get back to you soon."
}
```

**Database Action:**
- Inserts into `contact_submissions` table
- Status defaults to 'new'

---

### 2. Donation Form (`/api/donate.php`)

**Method:** POST

**Parameters:**
- `name` (required)
- `email` (required, validated)
- `amount` (required)

**Response:**
```json
{
  "success": true,
  "message": "Thank you for your donation! We will contact you shortly for payment processing."
}
```

**Note:** Payment processing not implemented. Currently just acknowledges donation.

---

### 3. Volunteer Application (`/api/volunteer.php`)

**Method:** POST

**Parameters:**
- `firstname` (required)
- `lastname` (required)
- `email` (required, validated)
- `phone` (optional)
- `message` (optional)

**Response:**
```json
{
  "success": true,
  "message": "Thank you for your interest in volunteering! We will contact you soon."
}
```

**Database Action:**
- Inserts into `contact_submissions` table
- Message field contains: "Volunteer Application\n\nPhone: [phone]\n\n[message]"

---

### 4. Newsletter Subscription (`/api/newsletter.php`)

**Method:** POST

**Parameters:**
- `email` (required, validated)

**Response:**
```json
{
  "success": true,
  "message": "Thank you for subscribing!"
}
```

**Database Action:**
- Inserts into `newsletter_subscriptions` table
- Uses `ON DUPLICATE KEY UPDATE status = 'active'` to reactivate existing subscriptions

---

### 5. Search API (`/api/search.php`)

**Method:** GET

**Parameters:**
- `q` (required, minimum 2 characters) - Search query
- `limit` (optional, default 10) - Maximum results

**Response:**
```json
{
  "results": [
    {
      "id": 1,
      "title": "Example Title",
      "description": "Truncated description...",
      "type": "Cause|Initiative|Event|Impact",
      "url": "/causes.php or /initiatives.php?initiative=1",
      "icon": "target|lightbulb|calendar|heart"
    }
  ]
}
```

**Search Scope:**
- Causes (title, description)
- Initiatives (title, description)
- Events (title, description)
- Impact Activities (title, description)

**Features:**
- Removes duplicates based on title
- Limits total results
- Returns type-specific URLs and icons

---

## Frontend Assets & Dependencies

### CSS Files
1. **bootstrap.min.css** - Bootstrap 4 framework
2. **style.css** - Main stylesheet
3. **admin.css** - Admin dashboard styles
4. **loader.css** - Page loader animation
5. **search.css** - Search functionality styles
6. **impact.css** - Impact page specific styles
7. **events.css** - Events page specific styles
8. **initiatives.css** - Initiatives page specific styles
9. **values-approach.css** - Values and approach sections
10. **ghi-colors.css** - Brand color definitions
11. **inline-styles.css** - Additional inline styles
12. **overrides.css** - Style overrides

### JavaScript Libraries
1. **jquery-3.3.1.min.js** - jQuery core
2. **jquery-migrate-3.0.1.min.js** - jQuery migration
3. **popper.min.js** - Popper.js for Bootstrap
4. **bootstrap.min.js** - Bootstrap JavaScript
5. **owl.carousel.min.js** - Owl Carousel for sliders
6. **jquery.sticky.js** - Sticky navigation
7. **jquery.waypoints.min.js** - Scroll waypoints
8. **jquery.animateNumber.min.js** - Number animations
9. **jquery.fancybox.min.js** - Lightbox functionality
10. **jquery.easing.1.3.js** - Easing functions
11. **aos.js** - Animate On Scroll library
12. **main.js** - Main JavaScript file
13. **search.js** - Search functionality

### Custom JavaScript Features

#### 1. Page Loader
- Animated loader with progress bar
- Shows on page load
- Fades out after content loads
- Triggered on AJAX navigation

#### 2. AJAX Navigation
- Intercepts internal links
- Loads content via Fetch API
- Updates URL without full page reload
- Reinitializes scripts (AOS, etc.)
- Falls back to normal navigation on error

#### 3. Search Functionality
- Real-time search as user types
- AJAX requests to `/api/search.php`
- Dropdown results display
- Click to navigate to result
- Debounced input (in search.js)

---

## Configuration

### Main Configuration (`config/config.php`)

**Environment Settings:**
- `ENVIRONMENT`: 'development' or 'production' (from `APP_ENV` env var)
- Error reporting based on environment

**Base Paths:**
- `BASE_PATH`: Project root directory
- `BASE_URL`: Site URL (from `BASE_URL` env var or default)

**Database Configuration:**
- `DB_HOST`: Database host (from `DB_HOST` env var or 'localhost')
- `DB_NAME`: Database name (from `DB_NAME` env var or 'global_harmony_initiative')
- `DB_USER`: Database user (from `DB_USER` env var or 'root')
- `DB_PASS`: Database password (from `DB_PASS` env var or '')
- `DB_CHARSET`: 'utf8mb4'

**Site Configuration:**
- `SITE_NAME`: 'Global Harmony Initiative Inc.'
- `SITE_TAGLINE`: 'Bridging Global Compassion with Local Action'
- `SITE_EMAIL`: 'info@globalharmonyinitiative.org'
- `SITE_PHONE_US`: '+1 (xxx) xxx-xxxx'
- `SITE_PHONE_EA`: '+254 (xxx) xxx-xxx'

**Path Definitions:**
- `ASSETS_PATH`: Base path + '/assets'
- `UPLOADS_PATH`: Base path + '/uploads'
- `LOGO_PATH`: Base path + '/Logo'

**URL Definitions:**
- `ASSETS_URL`: BASE_URL + '/assets'
- `UPLOADS_URL`: BASE_URL + '/uploads'
- `LOGO_URL`: BASE_URL + '/Logo'

**Security:**
- `ADMIN_SESSION_NAME`: 'ghi_admin_session'
- `SESSION_LIFETIME`: 3600 * 24 (24 hours)

**Timezone:**
- `America/New_York`

---

### Database Configuration (`config/database.php`)

**Database Class:**
- Singleton pattern
- PDO connection with error handling
- UTF-8 charset
- Prepared statements enabled
- `createTables()` method for initial setup

---

## File Upload System

### Upload Process

1. **File Validation:**
   - Allowed extensions: `jpg`, `jpeg`, `png`, `gif`, `webp`
   - File size limits (PHP `upload_max_filesize` and `post_max_size`)
   - Error checking via `$_FILES['field']['error']`

2. **File Naming:**
   - Unique filename: `timestamp_random.extension`
   - Example: `1704067200_a3f5b2c1.jpg`

3. **Storage:**
   - Files stored in `/uploads/` directory
   - Filename stored in database (not full path)
   - Images accessed via: `BASE_URL . '/uploads/' . $filename`

4. **Upload Locations:**
   - **Causes**: `image` field → `/uploads/`
   - **Initiatives**: `image` field → `/uploads/`
   - **Events**: `image` field → `/uploads/`
   - **Impact Activities**: `thumbnail` and `image` fields → `/uploads/`

5. **Fallback Images:**
   - If no image uploaded, uses placeholder images:
     - Causes: `img_1.jpg`, `img_2.jpg`, `img_3.jpg` (cycles)
     - Events/Impact: Logo placeholder from `/Logo/Square-White-BG.png`

6. **Permissions:**
   - `/uploads/` directory must be writable (chmod 755 or 777)

---

## Search Functionality

### Frontend (`js/search.js`)

**Features:**
- Real-time search as user types
- Debounced input (waits for user to stop typing)
- AJAX request to `/api/search.php`
- Dropdown results display
- Click to navigate
- Keyboard navigation (arrow keys, Enter, Escape)

**Search Input:**
- Located in header (desktop only)
- Placeholder: "Search..."
- Icon: search icon

**Results Display:**
- Dropdown below search input
- Shows title, description, type
- Type-specific icons
- Maximum 10 results (configurable)

---

### Backend (`api/search.php`)

**Search Logic:**
1. Validates query (minimum 2 characters)
2. Searches across 4 tables:
   - `causes` (title, description)
   - `initiatives` (title, description)
   - `events` (title, description)
   - `impact_activities` (title, description)
3. Uses `LIKE` with wildcards: `%query%`
4. Removes duplicates based on title
5. Limits total results
6. Returns JSON with results array

**Performance:**
- Uses prepared statements
- Limits results per table
- Indexes on title/description fields recommended for production

---

## Implementation Notes

### For Template Migration

When implementing these features on a new template:

1. **Database Setup:**
   - Run `setup-database.php` or manually create tables
   - Ensure all foreign key relationships are maintained
   - Set up proper indexes for performance

2. **Configuration:**
   - Copy `config/config.php` and update paths/URLs
   - Set database credentials
   - Configure environment variables for production

3. **File Structure:**
   - Maintain directory structure for includes, api, admin
   - Ensure `/uploads/` directory is writable
   - Copy all CSS/JS files or adapt to new template's structure

4. **Dynamic Content Integration:**
   - Replace static content with database queries
   - Use helper functions (`e()`, `formatDate()`, etc.) for output
   - Implement status-based filtering
   - Add breadcrumb navigation where needed

5. **Admin Panel:**
   - Implement authentication (session-based)
   - Create CRUD interfaces for all content types
   - Add image upload functionality
   - Implement status management (draft/published)

6. **API Endpoints:**
   - Ensure all forms submit to API endpoints
   - Return JSON responses
   - Handle errors gracefully
   - Validate all input

7. **Frontend Integration:**
   - Integrate search functionality
   - Implement AJAX navigation (optional)
   - Add page loader (optional)
   - Ensure responsive design

8. **Image Handling:**
   - Implement file upload validation
   - Generate unique filenames
   - Store filenames in database
   - Use fallback images when needed

9. **Constants:**
   - Move hardcoded content to constants file
   - Update objectives, values, quotes as needed

10. **Testing:**
    - Test all forms and API endpoints
    - Verify database relationships
    - Test image uploads
    - Test search functionality
    - Test admin panel CRUD operations

---

### Key Implementation Points

1. **Security:**
   - Always use prepared statements (PDO)
   - Escape output with `e()` function
   - Validate and sanitize all input
   - Change default admin password
   - Use environment variables for sensitive data

2. **Performance:**
   - Add database indexes on frequently queried fields
   - Limit query results with LIMIT
   - Use JOINs efficiently
   - Cache static content if possible

3. **User Experience:**
   - Show loading states for forms
   - Display success/error messages
   - Provide fallback images
   - Ensure mobile responsiveness

4. **Maintenance:**
   - Regular database backups
   - Monitor uploads directory size
   - Keep dependencies updated
   - Log errors in production

---

## Additional Information

### Default Admin User
- Created by `setup-database.php`
- Username: `admin`
- Password: `admin123`
- **MUST BE CHANGED** after first login

### Environment Variables
Can be set via environment variables or `.env` file:
- `APP_ENV`: Environment (development/production)
- `BASE_URL`: Site base URL
- `DB_HOST`: Database host
- `DB_NAME`: Database name
- `DB_USER`: Database user
- `DB_PASS`: Database password

### Composer Autoloading
- PSR-4 autoloading for `GHI\` namespace in `src/` directory
- Files autoloaded: `config/config.php`, `includes/constants.php`, `includes/functions.php`

### Session Management
- Admin sessions use `ghi_admin_session` name
- 24-hour session lifetime
- Session started in admin pages

### Error Handling
- Development: Errors displayed
- Production: Errors logged, not displayed
- PDO exceptions caught and handled gracefully

---

## Conclusion

This documentation provides a comprehensive overview of the Global Harmony Initiative website structure, features, and implementation details. Use this as a reference when migrating to a new template or implementing similar functionality.

For questions or clarifications, refer to the source code or contact the development team.

---

**Last Updated:** 2025
**Version:** 1.0

