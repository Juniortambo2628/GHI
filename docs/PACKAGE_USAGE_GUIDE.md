# Package Usage Guide
## Global Harmony Initiative - Comprehensive Package Integration

This document provides a comprehensive guide on how to use all installed packages across the codebase.

---

## PHP Packages

### 1. **Rector** - Code Refactoring
**Location:** `vendor/rector/rector`  
**Usage:**
```bash
# Check what would be refactored (dry-run)
composer rector

# Apply refactoring
composer rector:refactor
```

**Configuration:** `rector.php`

---

### 2. **PHP-CS-Fixer** - Code Style
**Location:** `vendor/friendsofphp/php-cs-fixer`  
**Usage:**
```bash
# Check code style issues
composer cs-check

# Fix code style issues
composer cs-fix
```

**Configuration:** `.php-cs-fixer.php`

---

### 3. **PHPStan** - Static Analysis
**Location:** `vendor/phpstan/phpstan`  
**Usage:**
```bash
composer phpstan
```

**Configuration:** `phpstan.neon` (Level 5)

---

### 4. **PHPMD** - Mess Detector
**Location:** `vendor/phpmd/phpmd`  
**Usage:**
```bash
composer phpmd
```

**Configuration:** `phpmd.xml`

---

### 5. **Symfony VarDumper** - Debugging
**Location:** `vendor/symfony/var-dumper`  
**Usage in Code:**
```php
// Dump variable (returns value)
dump($variable);

// Dump and die
dd($variable);

// Multiple variables
dump($var1, $var2, $var3);
```

**Note:** Only works in development environment.

---

### 6. **Doctrine DBAL** - Database Abstraction
**Location:** `src/Services/DatabaseService.php`  
**Usage:**
```php
use GHI\Services\DatabaseService;

// Get connection
$connection = DatabaseService::getConnection();

// Execute query
$result = $connection->fetchAllAssociative('SELECT * FROM causes WHERE status = ?', ['active']);

// Get PDO instance (for backward compatibility)
$pdo = DatabaseService::getPdo();
```

---

### 7. **Symfony Validator** - Validation
**Location:** `src/Services/ValidationService.php`  
**Usage:**
```php
use GHI\Services\ValidationService;

// Validate email
$errors = ValidationService::validateEmail($email);

// Validate data against rules
$errors = ValidationService::validate($data, [
    'name' => ['required' => true, 'min' => 2],
    'email' => ['required' => true, 'email' => true],
]);

// Helper function
$errors = validate($data, $rules);
```

---

### 8. **Delight Auth** - Authentication
**Location:** `src/Services/AuthService.php`  
**Usage:**
```php
use GHI\Services\AuthService;

// Login
AuthService::login($email, $password);

// Check if logged in
if (AuthService::isLoggedIn()) {
    $userId = AuthService::getUserId();
}

// Logout
AuthService::logout();

// Helper functions
is_logged_in();
get_user_id();
require_login();
```

---

### 9. **Symfony Mailer** - Email Sending
**Location:** `src/Services/MailService.php`  
**Usage:**
```php
use GHI\Services\MailService;

// Send email
MailService::send($to, $subject, $body, $from, $fromName, $isHtml);

// Send contact form email
MailService::sendContactForm($name, $email, $subject, $message);

// Helper function
send_email($to, $subject, $body);
```

---

### 10. **Symfony Filesystem** - File Operations
**Location:** `src/Services/FileService.php` (uses Flysystem)  
**Usage:**
```php
use GHI\Services\FileService;

// Upload file
$result = FileService::upload($file, $destinationPath, $allowedTypes);

// Check if file exists
if (FileService::exists($path)) {
    // ...
}

// Delete file
FileService::delete($path);

// Helper functions
upload_file($file, $path, $types);
file_exists_fs($path);
delete_file_fs($path);
```

---

### 11. **League Flysystem** - File Storage Abstraction
**Location:** `src/Services/FileService.php`  
**Usage:** See FileService above. Provides abstraction for local, S3, FTP, etc.

---

### 12. **Symfony Security CSRF** - CSRF Protection
**Location:** `src/Services/CsrfService.php`  
**Usage:**
```php
use GHI\Services\CsrfService;

// Generate token
$token = CsrfService::generateToken('form');

// Validate token
if (CsrfService::validateToken($token, 'form')) {
    // Valid
}

// Helper functions
csrf_token('form');
csrf_field('form');
csrf_validate($token, 'form');
```

---

### 13. **Monolog** - Logging
**Location:** `src/Services/LoggerService.php`  
**Usage:**
```php
// Log message
log_message('info', 'User logged in', ['user_id' => 123]);
log_message('error', 'Database error', ['error' => $e->getMessage()]);

// Available levels:
// debug, info, notice, warning, error, critical, alert, emergency
```

---

### 14. **Symfony Dotenv** - Environment Variables
**Location:** `config/config.php`  
**Usage:** Automatically loaded. Access via:
```php
$_ENV['DB_HOST'] ?? getenv('DB_HOST')
```

---

### 15. **Guzzle** - HTTP Client
**Location:** `src/Services/HttpService.php`  
**Usage:**
```php
use GHI\Services\HttpService;

// GET request
$response = HttpService::get($url, $options);

// POST request
$response = HttpService::post($url, $data, $options);

// Helper functions
http_get($url, $options);
http_post($url, $data, $options);
http_download($url, $destination);
```

---

### 16. **Symfony Cache** - Caching
**Location:** `src/Services/CacheService.php`  
**Usage:**
```php
use GHI\Services\CacheService;

// Get from cache
$value = CacheService::get($key, $callback, $lifetime);

// Set cache
CacheService::set($key, $value, $lifetime);

// Remember (get or execute and cache)
$value = CacheService::remember($key, $callback, $lifetime);

// Helper functions
cache_get($key, $callback, $lifetime);
cache_set($key, $value, $lifetime);
cache_remember($key, $callback, $lifetime);
```

---

### 17. **Twig** - Templating Engine
**Location:** `src/Services/TemplateService.php`  
**Usage:**
```php
use GHI\Services\TemplateService;

// Render template
$html = TemplateService::render('template.twig', ['variable' => $value]);

// Display template
TemplateService::display('template.twig', ['variable' => $value]);

// Helper functions
render_template('template.twig', $variables);
display_template('template.twig', $variables);
```

---

### 18. **Symfony Event Dispatcher** - Event System
**Location:** `src/Services/EventService.php`  
**Usage:**
```php
use GHI\Services\EventService;

// Dispatch event
$event = new \GHI\Events\UserLoggedInEvent($userId, $email);
EventService::dispatch($event, UserLoggedInEvent::NAME);

// Listen to event
EventService::listen(UserLoggedInEvent::NAME, function ($event) {
    // Handle event
}, 0);

// Helper functions
event_dispatch($event, $eventName);
event_listen($eventName, $listener, $priority);
```

---

## NPM Packages

### 1. **ESLint** - JavaScript Linting
**Location:** `node_modules/eslint`  
**Usage:**
```bash
npm run lint        # Check for issues
npm run lint:fix    # Auto-fix issues
```

**Configuration:** `.eslintrc.json`

---

### 2. **Prettier** - Code Formatting
**Location:** `node_modules/prettier`  
**Usage:**
```bash
npm run format
```

**Configuration:** `.prettierrc.json`

---

### 3. **JSCPD** - Duplicate Code Detection
**Location:** `node_modules/jscpd`  
**Usage:**
```bash
npm run check-duplicates
```

---

### 4. **Vite** - Build Tool
**Location:** `node_modules/vite`  
**Usage:**
```bash
npm run dev      # Development server
npm run build    # Production build
npm run preview  # Preview production build
```

**Configuration:** `vite.config.js`

---

### 5. **Axios** - HTTP Client
**Location:** `js/api.js`  
**Usage:**
```javascript
import apiService from './api.js';

// GET request
const response = await apiService.get('/api/endpoint');

// POST request
const response = await apiService.post('/api/endpoint', { data: 'value' });

// Upload file
const response = await apiService.upload('/api/upload', formData, (progress) => {
    console.log('Upload progress:', progress);
});
```

---

### 6. **Zod** - Schema Validation
**Location:** `js/validation.js`  
**Usage:**
```javascript
import { validate, formSchemas } from './validation.js';

// Validate form data
const result = validate(formSchemas.contact, formData);

if (result.success) {
    // Valid data
    console.log(result.data);
} else {
    // Show errors
    console.log(result.errors);
}
```

---

### 7. **Day.js** - Date Manipulation
**Location:** `js/utils.js`  
**Usage:**
```javascript
import utils from './utils.js';

// Format date
const formatted = utils.date.format(new Date(), 'YYYY-MM-DD');

// Relative time
const relative = utils.date.fromNow(date);

// Add time
const future = utils.date.add(date, 1, 'month');
```

---

### 8. **Lodash-ES** - Utility Functions
**Location:** `js/utils.js`  
**Usage:**
```javascript
import utils from './utils.js';

// Debounce
const debouncedFn = utils.debounce(() => {
    // Function logic
}, 300);

// Throttle
const throttledFn = utils.throttle(() => {
    // Function logic
}, 100);

// Clone
const cloned = utils.clone(object);

// Check if empty
if (utils.isEmpty(value)) {
    // ...
}
```

---

### 9. **Notyf** - Toast Notifications
**Location:** `js/notifications.js`  
**Usage:**
```javascript
import notifications from './notifications.js';

// Success notification
notifications.success('Operation successful!');

// Error notification
notifications.error('An error occurred');

// Warning notification
notifications.warning('Please check your input');

// Info notification
notifications.info('New update available');
```

---

### 10. **Form Serialize** - Form Data Serialization
**Location:** `js/utils.js`  
**Usage:**
```javascript
import utils from './utils.js';

// Serialize form to object
const formData = utils.serializeForm(formElement);

// Serialize form to string
const formString = utils.serializeFormString(formElement);
```

---

### 11. **MicroModal** - Modal Dialogs
**Location:** `js/modals.js`  
**Usage:**
```javascript
import modalService from './modals.js';

// Show modal
modalService.show('modal-id');

// Close modal
modalService.close('modal-id');

// Update modal content
modalService.updateContent('modal-id', 'Title', 'Body', 'Footer');
```

**HTML:**
```html
<button data-micromodal-trigger="modal-id">Open Modal</button>
<div id="modal-id" class="modal">
    <div class="modal-content">
        <button data-micromodal-close>Close</button>
    </div>
</div>
```

---

### 12. **FilePond** - File Uploads
**Location:** `js/file-upload.js`  
**Usage:**
```javascript
import fileUploadService from './file-upload.js';

// Initialize image upload
const pond = fileUploadService.initImage(inputElement, {
    server: {
        url: '/api/upload/image',
    },
});

// Initialize document upload
const pond = fileUploadService.initDocument(inputElement);

// Initialize generic upload
const pond = fileUploadService.init(inputElement, options);
```

**HTML:**
```html
<input type="file" data-filepond="image">
```

---

### 13. **Tabulator** - Data Tables
**Location:** `js/tables.js`  
**Usage:**
```javascript
import tableService from './tables.js';

// Initialize from AJAX
const table = tableService.initFromAjax(container, '/api/data', columns);

// Initialize from data
const table = tableService.initFromData(container, data, columns);

// Export table
tableService.export(table, 'csv', 'export');
```

**HTML:**
```html
<div data-tabulator data-ajax-url="/api/data" data-columns='[{"title":"Name","field":"name"}]'></div>
```

---

### 14. **Quill** - Rich Text Editor
**Location:** `js/editor.js`  
**Usage:**
```javascript
import editorService from './editor.js';

// Initialize editor
const editor = editorService.init(container, {
    placeholder: 'Start typing...',
});

// Get content
const html = editorService.getContent(editor);

// Set content
editorService.setContent(editor, '<p>Hello</p>');
```

**HTML:**
```html
<div data-quill-editor data-placeholder="Start typing..."></div>
```

---

### 15. **Chart.js** - Charts
**Location:** `js/charts.js`  
**Usage:**
```javascript
import chartService from './charts.js';

// Create line chart
const chart = chartService.line(canvas, data, options);

// Create bar chart
const chart = chartService.bar(canvas, data, options);

// Create pie chart
const chart = chartService.pie(canvas, data, options);
```

**HTML:**
```html
<canvas data-chart="line" data-chart-data='{"labels":["Jan","Feb"],"datasets":[{"data":[10,20]}]}'></canvas>
```

---

### 16. **Zustand** - State Management
**Location:** `js/store.js`  
**Usage:**
```javascript
import { useStore } from './store.js';

// In component/module
const { user, setUser, isLoading, setLoading } = useStore();

// Set user
setUser({ id: 1, name: 'John' });

// Set loading
setLoading(true);
```

---

### 17. **Sentry** - Error Tracking
**Location:** `js/error-tracking.js`  
**Usage:**
```javascript
import errorTracking from './error-tracking.js';

// Initialize
errorTracking.init(dsn, options);

// Capture exception
errorTracking.captureException(error, {
    tags: { section: 'checkout' },
    extra: { userId: 123 },
});

// Capture message
errorTracking.captureMessage('Something went wrong', 'error');
```

---

### 18. **GSAP** - Animations
**Location:** `js/animations.js`  
**Usage:**
```javascript
import animations from './animations.js';

// Fade in
animations.fadeIn(element, 1, 0);

// Slide in
animations.slideIn(element, 'left', 1, 0);

// Scale
animations.scale(element, 0, 1, 1, 0);

// Stagger
animations.stagger(elements, { opacity: 0, y: 20 }, 0.1);
```

**HTML:**
```html
<div data-animate-on-scroll="fadeIn" data-duration="1" data-delay="0"></div>
```

---

## Best Practices

1. **Always use service classes** instead of direct package usage
2. **Use helper functions** when available (defined in `includes/functions.php`)
3. **Follow DRY principles** - don't repeat code
4. **Use TypeScript** for new JavaScript code (optional but recommended)
5. **Run quality checks** before committing:
   ```bash
   composer quality
   npm run lint
   npm run format
   ```

---

## Integration Order

1. **Development Tools** (Rector, PHP-CS-Fixer, PHPStan, PHPMD, ESLint, Prettier)
2. **Core Services** (Database, Validation, Auth, Mail, File, Cache)
3. **Frontend Packages** (Axios, Zod, Day.js, Lodash)
4. **UI Components** (Notyf, MicroModal, FilePond, Tabulator, Quill, Chart.js)
5. **Advanced Features** (GSAP, Sentry, Zustand)

---

## Maintenance

- **Update packages regularly**: `composer update` and `npm update`
- **Run quality checks**: `composer quality` and `npm run lint`
- **Check for duplicates**: `npm run check-duplicates`
- **Review dependencies**: Remove unused packages

---

## Support

For issues or questions about package usage, refer to:
- Package documentation in `vendor/` or `node_modules/`
- Service classes in `src/Services/`
- Helper functions in `includes/functions.php`

