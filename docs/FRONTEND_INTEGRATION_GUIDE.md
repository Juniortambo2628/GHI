# Frontend Integration Guide

## Overview

This guide explains how to use the newly integrated frontend modules in your HTML pages.

## Available Modules

### 1. API Service (`api.js`)
HTTP client using Axios with CSRF token support.

**Usage:**
```javascript
import apiService from './js/api.js';

// GET request
const response = await apiService.get('/api/data');

// POST request
const response = await apiService.post('/api/data', { name: 'John' });

// File upload with progress
const response = await apiService.upload('/api/upload', formData, (progress) => {
  console.log(`Upload progress: ${progress}%`);
});
```

### 2. Validation Service (`validation.js`)
Schema validation using Zod.

**Usage:**
```javascript
import { validate, formSchemas } from './js/validation.js';

// Validate form data
const result = validate(formSchemas.contact, {
  name: 'John Doe',
  email: 'john@example.com',
  subject: 'Hello',
  message: 'This is a test message'
});

if (result.success) {
  // Form is valid
} else {
  // Display errors: result.errors
}
```

### 3. Notifications (`notifications.js`)
Toast notifications using Notyf.

**Usage:**
```javascript
import notifications from './js/notifications.js';

notifications.success('Operation completed!');
notifications.error('Something went wrong!');
notifications.warning('Please check your input');
notifications.info('New update available');
```

### 4. Modals (`modals.js`)
Modal dialogs using MicroModal.

**HTML:**
```html
<!-- Trigger button -->
<button data-micromodal-trigger="my-modal">Open Modal</button>

<!-- Modal -->
<div class="modal micromodal-slide" id="my-modal" aria-hidden="true">
  <div class="modal__overlay" tabindex="-1" data-micromodal-close>
    <div class="modal__container" role="dialog" aria-modal="true">
      <header class="modal__header">
        <h2 class="modal__title">Modal Title</h2>
        <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
      </header>
      <main class="modal__content">
        <p>Modal content here</p>
      </main>
      <footer class="modal__footer">
        <button data-micromodal-close>Close</button>
      </footer>
    </div>
  </div>
</div>
```

**JavaScript:**
```javascript
import modalService from './js/modals.js';

// Show modal
modalService.show('my-modal');

// Close modal
modalService.close('my-modal');

// Update modal content
modalService.updateContent('my-modal', 'New Title', '<p>New content</p>');
```

### 5. File Upload (`file-upload.js`)
File uploads using FilePond.

**HTML:**
```html
<!-- Image upload -->
<input type="file" data-filepond="image" />

<!-- Document upload -->
<input type="file" data-filepond="document" />

<!-- Generic file upload -->
<input type="file" data-filepond="file" />
```

**JavaScript:**
```javascript
import fileUploadService from './js/file-upload.js';

// Initialize image upload
const pond = fileUploadService.initImage(inputElement, {
  server: {
    url: '/api/upload/image',
  },
});

// Initialize document upload
const pond = fileUploadService.initDocument(inputElement, {
  server: {
    url: '/api/upload/document',
  },
});
```

### 6. Data Tables (`tables.js`)
Data tables using Tabulator.

**HTML:**
```html
<div 
  data-tabulator
  data-ajax-url="/api/data"
  data-columns='[
    {"title": "Name", "field": "name", "sorter": "string"},
    {"title": "Email", "field": "email", "sorter": "string"},
    {"title": "Date", "field": "date", "sorter": "date"}
  ]'
></div>
```

**JavaScript:**
```javascript
import tableService from './js/tables.js';

// Initialize from AJAX
const table = tableService.initFromAjax(container, '/api/data', [
  { title: 'Name', field: 'name' },
  { title: 'Email', field: 'email' }
]);

// Initialize from data array
const table = tableService.initFromData(container, dataArray, columns);

// Export table
tableService.export(table, 'csv', 'export');
```

### 7. Rich Text Editor (`editor.js`)
Rich text editor using Quill.

**HTML:**
```html
<div data-quill-editor data-placeholder="Start typing..."></div>
```

**JavaScript:**
```javascript
import editorService from './js/editor.js';

// Initialize editor
const editor = editorService.init(container, {
  placeholder: 'Start typing...'
});

// Get content
const html = editorService.getContent(editor);
const text = editorService.getText(editor);

// Set content
editorService.setContent(editor, '<p>Hello World</p>');
```

### 8. Charts (`charts.js`)
Charts using Chart.js.

**HTML:**
```html
<canvas 
  data-chart="bar"
  data-chart-data='{
    "labels": ["Jan", "Feb", "Mar"],
    "datasets": [{
      "label": "Sales",
      "data": [10, 20, 30]
    }]
  }'
  data-chart-options='{"title": "Monthly Sales"}'
></canvas>
```

**JavaScript:**
```javascript
import chartService from './js/charts.js';

// Create line chart
const chart = chartService.line(canvas, {
  labels: ['Jan', 'Feb', 'Mar'],
  datasets: [{
    label: 'Sales',
    data: [10, 20, 30]
  }]
}, { title: 'Monthly Sales' });

// Update chart
chartService.update(chart, newData);
```

### 9. Utilities (`utils.js`)
Utility functions using dayjs and lodash-es.

**Usage:**
```javascript
import utils from './js/utils.js';

// Date formatting
utils.date.format(new Date(), 'YYYY-MM-DD');
utils.date.fromNow(new Date());

// Form serialization
const data = utils.serializeForm(formElement);

// Debounce/throttle
const debouncedFn = utils.debounce(myFunction, 300);
const throttledFn = utils.throttle(myFunction, 300);
```

## Auto-Initialization

The `modern-main.js` file automatically initializes components based on data attributes:

- `[data-filepond]` - File uploads
- `[data-quill-editor]` - Rich text editors
- `[data-tabulator]` - Data tables
- `[data-chart]` - Charts

## Including in HTML

Add the modern JavaScript bundle to your page:

```html
<script type="module" src="/js/modern-main.js"></script>
```

Or use the Vite build output:

```html
<script type="module" src="/dist/js/modern-main.js"></script>
```

## Examples

### Complete Form with Validation and Upload

```html
<form id="contact-form">
  <input type="text" name="name" required>
  <input type="email" name="email" required>
  <textarea name="message" required></textarea>
  <input type="file" data-filepond="image" name="attachment">
  <button type="submit">Submit</button>
</form>

<script type="module">
import apiService from './js/api.js';
import { validate, formSchemas } from './js/validation.js';
import notifications from './js/notifications.js';

document.getElementById('contact-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  
  // Validate
  const validation = validate(formSchemas.contact, Object.fromEntries(formData));
  if (!validation.success) {
    notifications.error('Please fix the errors');
    return;
  }
  
  // Submit
  const response = await apiService.post('/api/contact', formData);
  if (response.success) {
    notifications.success('Message sent!');
  }
});
</script>
```

## Next Steps

- Integrate Sentry for error tracking
- Add GSAP for animations
- Set up Zustand for state management (optional)

