# Integration Examples
## How to Use the New Packages

---

## 1. Image Compression (Already Integrated)

Image compression is now **automatically enabled** for all FilePond image uploads. No additional code needed!

**What happens:**
- Images are compressed client-side before upload (60-80% size reduction)
- Server-side processing optimizes images further (creates thumbnails)
- Uploads are faster and use less storage

**To customize compression:**
```javascript
// In js/file-upload.js, modify the compressImage call:
beforeAddFile: async (file) => {
  if (file.fileType === 'image') {
    return await compressImage(file.file, {
      maxSizeMB: 0.5,        // Smaller max size
      maxWidthOrHeight: 1600, // Smaller dimensions
    });
  }
  return file;
}
```

---

## 2. Excel Export

### Export Tabulator Table to Excel

```javascript
// Import the export utility
import { exportTabulatorToExcel } from '/dist/js/excel-export.js';

// Get your Tabulator instance
const table = new Tabulator('#causesTable', {
  // ... your table config
});

// Add export button
document.getElementById('exportExcelBtn').addEventListener('click', () => {
  exportTabulatorToExcel(table, 'causes-export.xlsx');
});
```

### Export Array Data to Excel

```javascript
import { exportToExcel } from '/dist/js/excel-export.js';

const data = [
  { Name: 'John Doe', Email: 'john@example.com', Role: 'Admin' },
  { Name: 'Jane Smith', Email: 'jane@example.com', Role: 'User' },
];

exportToExcel(data, 'users-export.xlsx', 'Users');
```

### Export HTML Table to Excel

```javascript
import { exportTableToExcel } from '/dist/js/excel-export.js';

// Export any HTML table
exportTableToExcel('#myTable', 'table-export.xlsx');
```

---

## 3. PDF Generation

### Generate PDF from Data

```javascript
import { generatePDF } from '/dist/js/pdf-generator.js';

const data = [
  { Name: 'John Doe', Email: 'john@example.com', Role: 'Admin' },
  { Name: 'Jane Smith', Email: 'jane@example.com', Role: 'User' },
];

generatePDF({
  title: 'User Report',
  data,
  filename: 'users-report.pdf',
  orientation: 'portrait',
  showDate: true,
});
```

### Generate PDF from Tabulator Table

```javascript
import { generatePDFFromTabulator } from '/dist/js/pdf-generator.js';

const table = new Tabulator('#causesTable', {
  // ... table config
});

document.getElementById('exportPDFBtn').addEventListener('click', () => {
  generatePDFFromTabulator(table, 'causes-report.pdf');
});
```

### Generate PDF from HTML Element

```javascript
import { generatePDFFromHTML } from '/dist/js/pdf-generator.js';

generatePDFFromHTML('report-content', 'report.pdf', {
  orientation: 'landscape',
  format: 'a4',
});
```

---

## 4. Server-Side Image Processing

### Process Uploaded Image

```php
use GHI\Services\ImageService;

$imageService = new ImageService();

// Process uploaded image (resize, optimize, create thumbnail)
$result = $imageService->processUploadedImage('uploads/images/photo.jpg', [
    'maxWidth' => 1920,
    'maxHeight' => 1080,
    'quality' => 85,
    'createThumbnail' => true,
    'thumbnailSize' => 300,
]);

if ($result['success']) {
    echo "Original: {$result['originalSize']} bytes\n";
    echo "Processed: {$result['processedSize']} bytes\n";
    echo "Thumbnail: {$result['thumbnailSize']} bytes\n";
    echo "Thumbnail path: {$result['thumbnail']}\n";
}
```

### Create Thumbnail

```php
use GHI\Services\ImageService;

$imageService = new ImageService();
$imageService->createThumbnail(
    'uploads/images/photo.jpg',
    300,
    'uploads/images/thumbnails/photo_thumb.jpg'
);
```

### Optimize Image

```php
use GHI\Services\ImageService;

$imageService = new ImageService();
$imageService->optimize('uploads/images/photo.jpg', 85);
```

---

## 5. CSV Export (Server-Side)

### Download CSV

```php
use GHI\Services\CsvService;

$csvService = new CsvService();

$data = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
];

$csvService->download($data, 'users.csv');
```

### Save CSV to File

```php
use GHI\Services\CsvService;

$csvService = new CsvService();

$data = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
];

$csvService->write($data, 'exports/users.csv');
```

### Read CSV File

```php
use GHI\Services\CsvService;

$csvService = new CsvService();
$data = $csvService->read('imports/users.csv', hasHeader: true);

foreach ($data as $row) {
    echo $row['name'] . ' - ' . $row['email'] . "\n";
}
```

---

## 6. Rate Limiting

### Protect API Endpoint

```php
use GHI\Services\RateLimitService;

// At the top of your API endpoint
if (!RateLimitService::checkAndRespond('api', [
    'limit' => 100,
    'interval' => '1 minute',
    'amount' => 100,
])) {
    exit; // Response already sent
}

// Continue with API logic...
```

### Custom Rate Limit Check

```php
use GHI\Services\RateLimitService;

$clientKey = RateLimitService::getClientKey();

if (!RateLimitService::isAllowed($clientKey, 'upload', [
    'limit' => 10,
    'interval' => '1 hour',
    'amount' => 10,
])) {
    http_response_code(429);
    echo json_encode(['error' => 'Upload limit exceeded']);
    exit;
}

// Process upload...
```

### Get Remaining Requests

```php
use GHI\Services\RateLimitService;

$clientKey = RateLimitService::getClientKey();
$remaining = RateLimitService::getRemaining($clientKey, 'api');

header('X-RateLimit-Remaining: ' . $remaining);
```

---

## 7. Server-Side PDF Generation

### Generate PDF from HTML

```php
use GHI\Services\PdfService;

$pdfService = new PdfService();

$html = '<h1>Report</h1><p>This is a test report.</p>';
$pdfService->generateFromHTML($html, 'report.pdf');
```

### Generate PDF from Template

```php
use GHI\Services\PdfService;

$pdfService = new PdfService();

$pdfService->generateFromTemplate(
    __DIR__ . '/../Views/pdf/invoice.php',
    [
        'invoice' => $invoiceData,
        'customer' => $customerData,
    ],
    'invoice.pdf'
);
```

### Save PDF to File

```php
use GHI\Services\PdfService;

$pdfService = new PdfService();

$html = '<h1>Report</h1><p>Content here.</p>';
$pdfService->saveToFile($html, 'reports/monthly-report.pdf');
```

---

## 8. Admin Dashboard Integration Examples

### Add Export Buttons to Admin Tables

```html
<!-- In admin/causes.php or similar -->
<div class="d-flex justify-content-between mb-3">
    <h4>Causes</h4>
    <div>
        <button id="exportExcel" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </button>
        <button id="exportPDF" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </button>
    </div>
</div>
```

```javascript
// In admin/js/admin.js or similar
import { exportTabulatorToExcel } from '/dist/js/excel-export.js';
import { generatePDFFromTabulator } from '/dist/js/pdf-generator.js';

// After Tabulator initialization
document.getElementById('exportExcel')?.addEventListener('click', () => {
    exportTabulatorToExcel(window.causesTable, 'causes-export.xlsx');
});

document.getElementById('exportPDF')?.addEventListener('click', () => {
    generatePDFFromTabulator(window.causesTable, 'causes-report.pdf');
});
```

---

## 9. API Endpoint with Rate Limiting

```php
<?php
// admin/api/data-export.php

require_once __DIR__ . '/../../config/config.php';
require_login();

use GHI\Services\RateLimitService;
use GHI\Services\CsvService;

// Rate limit: 10 exports per hour
if (!RateLimitService::checkAndRespond('export', [
    'limit' => 10,
    'interval' => '1 hour',
    'amount' => 10,
])) {
    exit;
}

// Get data from database
$db = \GHI\Services\DatabaseService::getPdo();
$stmt = $db->query('SELECT * FROM causes');
$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// Export as CSV
$csvService = new CsvService();
$csvService->download($data, 'causes-export.csv');
```

---

## 10. Complete Example: Image Upload with Processing

The image upload endpoint (`admin/api/upload-image.php`) now automatically:
1. Receives compressed image from client
2. Processes image server-side (resize, optimize)
3. Creates thumbnail
4. Returns compression statistics

**Response includes:**
```json
{
    "success": true,
    "filename": "upload-1234567890-abc123.jpg",
    "path": "/uploads/images/upload-1234567890-abc123.jpg",
    "size": 245678,
    "original_size": 1024567,
    "compression": 76.0
}
```

---

## Next Steps

1. ✅ Image compression - **Already integrated**
2. ✅ Server-side image processing - **Already integrated**
3. Add export buttons to admin tables
4. Add rate limiting to API endpoints
5. Create PDF templates for reports

---

**All utilities are ready to use!** 🚀

