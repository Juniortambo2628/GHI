# New Packages Usage Guide
## Global Harmony Initiative - Implementation Guide

**Date**: December 2024  
**Status**: Ready for Implementation

---

## 📦 Installed Packages

### NPM Packages
- ✅ `browser-image-compression` (v2.0.2)
- ✅ `xlsx` (v0.18.5) ⚠️ *See security note below*
- ✅ `jspdf` (v3.0.3)
- ✅ `jspdf-autotable` (v5.0.2)

### Composer Packages
- ✅ `dompdf/dompdf` (v3.1.4)
- ✅ `intervention/image` (v3.11.4)
- ✅ `league/csv` (v9.27.1)
- ✅ `symfony/rate-limiter` (v7.3.2)
- ✅ `symfony/messenger` (v7.3.6)

---

## ⚠️ Security Note

The `xlsx` package has known vulnerabilities (prototype pollution and ReDoS). These are:
- **Low risk** for internal admin use
- **Higher risk** for public-facing features
- **No fix available** from the package maintainer

**Recommendations**:
1. Use `xlsx` only in admin dashboard (not public-facing)
2. Sanitize all user input before processing
3. Consider `papaparse` for CSV-only needs (more secure)
4. Monitor for package updates

---

## 🚀 Implementation Examples

### 1. Browser Image Compression (Client-Side)

**Use Case**: Compress images before upload to reduce file size and improve upload speed.

**Location**: `js/file-upload.js` or create `js/image-compression.js`

```javascript
import imageCompression from 'browser-image-compression';

/**
 * Compress image before upload
 * @param {File} file - Original image file
 * @param {Object} options - Compression options
 * @returns {Promise<File>} Compressed file
 */
export async function compressImage(file, options = {}) {
  const defaultOptions = {
    maxSizeMB: 1,              // Maximum file size in MB
    maxWidthOrHeight: 1920,    // Maximum width or height
    useWebWorker: true,        // Use web worker for better performance
    fileType: file.type,       // Preserve original file type
  };

  const compressionOptions = { ...defaultOptions, ...options };

  try {
    const compressedFile = await imageCompression(file, compressionOptions);
    console.log('Original size:', file.size / 1024 / 1024, 'MB');
    console.log('Compressed size:', compressedFile.size / 1024 / 1024, 'MB');
    return compressedFile;
  } catch (error) {
    console.error('Image compression failed:', error);
    // Fallback to original file
    return file;
  }
}

// Usage in FilePond setup
import { compressImage } from './image-compression.js';

FilePond.create(document.querySelector('input[type="file"]'), {
  beforeAddFile: async (file) => {
    if (file.fileType === 'image') {
      const compressed = await compressImage(file.file);
      return compressed;
    }
    return file;
  },
});
```

**Integration with FilePond**:
```javascript
// In admin/js/form-handler.js or js/file-upload.js
import FilePond from 'filepond';
import imageCompression from 'browser-image-compression';

FilePond.setOptions({
  beforeAddFile: async (file) => {
    if (file.fileType === 'image') {
      const compressed = await imageCompression(file.file, {
        maxSizeMB: 1,
        maxWidthOrHeight: 1920,
      });
      return compressed;
    }
    return file;
  },
});
```

---

### 2. Excel Export (Client-Side) - xlsx

**Use Case**: Export admin tables to Excel format.

**Location**: Create `js/excel-export.js`

```javascript
import * as XLSX from 'xlsx';

/**
 * Export data to Excel file
 * @param {Array} data - Array of objects to export
 * @param {string} filename - Output filename
 * @param {string} sheetName - Sheet name (default: 'Sheet1')
 */
export function exportToExcel(data, filename = 'export.xlsx', sheetName = 'Sheet1') {
  // Create workbook
  const wb = XLSX.utils.book_new();
  
  // Convert array of objects to worksheet
  const ws = XLSX.utils.json_to_sheet(data);
  
  // Add worksheet to workbook
  XLSX.utils.book_append_sheet(wb, ws, sheetName);
  
  // Write file
  XLSX.writeFile(wb, filename);
}

/**
 * Export Tabulator table to Excel
 * @param {Tabulator} table - Tabulator instance
 * @param {string} filename - Output filename
 */
export function exportTabulatorToExcel(table, filename = 'table-export.xlsx') {
  const data = table.getData();
  exportToExcel(data, filename);
}

// Usage in admin dashboard
import { exportTabulatorToExcel } from './excel-export.js';

// Add export button to Tabulator
const table = new Tabulator('#causesTable', {
  // ... table config
});

document.getElementById('exportExcel').addEventListener('click', () => {
  exportTabulatorToExcel(table, 'causes-export.xlsx');
});
```

**Security Considerations**:
- Only use in admin dashboard (authenticated users)
- Sanitize data before export
- Limit export size to prevent memory issues

---

### 3. PDF Generation (Client-Side) - jsPDF

**Use Case**: Generate PDFs for reports, invoices, or downloadable content.

**Location**: Create `js/pdf-generator.js`

```javascript
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

/**
 * Generate PDF from data
 * @param {Object} options - PDF options
 */
export function generatePDF(options = {}) {
  const {
    title = 'Report',
    data = [],
    filename = 'report.pdf',
    orientation = 'portrait', // 'portrait' or 'landscape'
  } = options;

  const doc = new jsPDF({
    orientation,
    unit: 'mm',
    format: 'a4',
  });

  // Add title
  doc.setFontSize(18);
  doc.text(title, 14, 20);

  // Add table if data provided
  if (data.length > 0) {
    autoTable(doc, {
      head: [Object.keys(data[0])],
      body: data.map(row => Object.values(row)),
      startY: 30,
      styles: { fontSize: 10 },
      headStyles: { fillColor: [0, 6, 86] }, // GHI primary color
    });
  }

  // Save PDF
  doc.save(filename);
}

/**
 * Generate PDF from HTML element
 * @param {string} elementId - ID of element to convert
 * @param {string} filename - Output filename
 */
export function generatePDFFromHTML(elementId, filename = 'export.pdf') {
  const element = document.getElementById(elementId);
  if (!element) {
    console.error('Element not found:', elementId);
    return;
  }

  const doc = new jsPDF({
    orientation: 'portrait',
    unit: 'mm',
    format: 'a4',
  });

  // Convert HTML to PDF (basic implementation)
  doc.html(element, {
    callback: (doc) => {
      doc.save(filename);
    },
    x: 10,
    y: 10,
    width: 190,
    windowWidth: 800,
  });
}

// Usage example
import { generatePDF } from './pdf-generator.js';

document.getElementById('exportPDF').addEventListener('click', () => {
  const data = [
    { Name: 'John Doe', Email: 'john@example.com', Role: 'Admin' },
    { Name: 'Jane Smith', Email: 'jane@example.com', Role: 'User' },
  ];

  generatePDF({
    title: 'User Report',
    data,
    filename: 'users-report.pdf',
  });
});
```

---

### 4. Server-Side PDF Generation - DomPDF

**Use Case**: Generate PDFs on the server for emails or server-side processing.

**Location**: Create `src/Services/PdfService.php`

```php
<?php

namespace GHI\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $this->dompdf = new Dompdf($options);
    }

    /**
     * Generate PDF from HTML string
     *
     * @param string $html HTML content
     * @param string $filename Output filename
     * @param bool $download Whether to download or return as string
     * @return string|void PDF content or download
     */
    public function generateFromHTML(string $html, string $filename = 'document.pdf', bool $download = true)
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        if ($download) {
            $this->dompdf->stream($filename, ['Attachment' => 1]);
        } else {
            return $this->dompdf->output();
        }
    }

    /**
     * Generate PDF from view template
     *
     * @param string $templatePath Path to template file
     * @param array $data Data to pass to template
     * @param string $filename Output filename
     * @return void
     */
    public function generateFromTemplate(string $templatePath, array $data = [], string $filename = 'document.pdf')
    {
        ob_start();
        extract($data);
        include $templatePath;
        $html = ob_get_clean();

        $this->generateFromHTML($html, $filename);
    }
}
```

**Usage Example**:
```php
use GHI\Services\PdfService;

$pdfService = new PdfService();

// Generate from HTML
$html = '<h1>Report</h1><p>This is a test report.</p>';
$pdfService->generateFromHTML($html, 'report.pdf');

// Generate from template
$pdfService->generateFromTemplate(
    __DIR__ . '/../Views/pdf/invoice.php',
    ['invoice' => $invoiceData],
    'invoice.pdf'
);
```

---

### 5. Image Processing (Server-Side) - Intervention Image

**Use Case**: Resize, crop, optimize images on the server.

**Location**: Create `src/Services/ImageService.php`

```php
<?php

namespace GHI\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Resize image
     *
     * @param string $imagePath Path to image
     * @param int $width Target width
     * @param int $height Target height
     * @param string $outputPath Output path (optional)
     * @return \Intervention\Image\Image
     */
    public function resize(string $imagePath, int $width, int $height, ?string $outputPath = null)
    {
        $image = $this->manager->read($imagePath);
        $image->scale($width, $height);

        if ($outputPath) {
            $image->save($outputPath);
        }

        return $image;
    }

    /**
     * Create thumbnail
     *
     * @param string $imagePath Path to image
     * @param int $size Thumbnail size (square)
     * @param string $outputPath Output path
     * @return void
     */
    public function createThumbnail(string $imagePath, int $size = 300, string $outputPath)
    {
        $image = $this->manager->read($imagePath);
        $image->cover($size, $size);
        $image->save($outputPath);
    }

    /**
     * Optimize image (reduce quality)
     *
     * @param string $imagePath Path to image
     * @param int $quality Quality (1-100)
     * @param string $outputPath Output path
     * @return void
     */
    public function optimize(string $imagePath, int $quality = 85, string $outputPath)
    {
        $image = $this->manager->read($imagePath);
        $image->save($outputPath, quality: $quality);
    }

    /**
     * Add watermark
     *
     * @param string $imagePath Path to image
     * @param string $watermarkPath Path to watermark image
     * @param string $position Position (top-left, top-right, bottom-left, bottom-right, center)
     * @param string $outputPath Output path
     * @return void
     */
    public function addWatermark(string $imagePath, string $watermarkPath, string $position = 'bottom-right', string $outputPath)
    {
        $image = $this->manager->read($imagePath);
        $watermark = $this->manager->read($watermarkPath);

        $image->place($watermark, $position, 10, 10);
        $image->save($outputPath);
    }
}
```

**Usage Example**:
```php
use GHI\Services\ImageService;

$imageService = new ImageService();

// Create thumbnail
$imageService->createThumbnail(
    '/path/to/original.jpg',
    300,
    '/path/to/thumbnail.jpg'
);

// Optimize image
$imageService->optimize(
    '/path/to/large-image.jpg',
    85,
    '/path/to/optimized-image.jpg'
);
```

---

### 6. CSV Handling - League CSV

**Use Case**: Import/export CSV files.

**Location**: Create `src/Services/CsvService.php`

```php
<?php

namespace GHI\Services;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Statement;

class CsvService
{
    /**
     * Read CSV file
     *
     * @param string $filePath Path to CSV file
     * @return array Array of rows
     */
    public function read(string $filePath): array
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $records = Statement::create()
            ->process($csv);

        return iterator_to_array($records);
    }

    /**
     * Write data to CSV file
     *
     * @param array $data Array of associative arrays
     * @param string $filePath Output file path
     * @return void
     */
    public function write(array $data, string $filePath): void
    {
        $csv = Writer::createFromPath($filePath, 'w+');
        
        if (!empty($data)) {
            // Write header
            $csv->insertOne(array_keys($data[0]));
            
            // Write data
            $csv->insertAll($data);
        }
    }

    /**
     * Download CSV file
     *
     * @param array $data Array of associative arrays
     * @param string $filename Output filename
     * @return void
     */
    public function download(array $data, string $filename = 'export.csv'): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $csv = Writer::createFromStream(fopen('php://output', 'w'));
        
        if (!empty($data)) {
            $csv->insertOne(array_keys($data[0]));
            $csv->insertAll($data);
        }
    }
}
```

**Usage Example**:
```php
use GHI\Services\CsvService;

$csvService = new CsvService();

// Export data
$data = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
];
$csvService->download($data, 'users.csv');

// Import data
$imported = $csvService->read('/path/to/import.csv');
```

---

### 7. Rate Limiting - Symfony Rate Limiter

**Use Case**: Protect API endpoints from abuse.

**Location**: Create `src/Services/RateLimitService.php`

```php
<?php

namespace GHI\Services;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\Policy\TokenBucketLimiter;

class RateLimitService
{
    private RateLimiterFactory $factory;

    public function __construct()
    {
        $storage = new InMemoryStorage();
        
        $this->factory = new RateLimiterFactory([
            'id' => 'api',
            'policy' => 'token_bucket',
            'limit' => 100, // Number of tokens
            'rate' => ['interval' => '1 minute', 'amount' => 100], // Refill rate
        ], $storage);
    }

    /**
     * Check if request is allowed
     *
     * @param string $key Unique identifier (e.g., IP address, user ID)
     * @return bool True if allowed, false if rate limited
     */
    public function isAllowed(string $key): bool
    {
        $limiter = $this->factory->create($key);
        $limit = $limiter->consume();

        return $limit->isAccepted();
    }

    /**
     * Get remaining tokens
     *
     * @param string $key Unique identifier
     * @return int Remaining tokens
     */
    public function getRemaining(string $key): int
    {
        $limiter = $this->factory->create($key);
        $limit = $limiter->consume();

        return $limit->getRemainingTokens();
    }
}
```

**Usage in API Endpoint**:
```php
use GHI\Services\RateLimitService;

$rateLimitService = new RateLimitService();
$clientKey = $_SERVER['REMOTE_ADDR']; // Or user ID

if (!$rateLimitService->isAllowed($clientKey)) {
    http_response_code(429);
    echo json_encode(['error' => 'Rate limit exceeded']);
    exit;
}

// Process request...
```

---

### 8. Message Queue - Symfony Messenger

**Use Case**: Process heavy operations asynchronously (emails, image processing, etc.).

**Location**: Create `src/Services/MessageService.php`

```php
<?php

namespace GHI\Services;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;

class MessageService
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Dispatch message to queue
     *
     * @param object $message Message object
     * @return Envelope
     */
    public function dispatch(object $message): Envelope
    {
        return $this->bus->dispatch($message);
    }
}
```

**Create Message Classes**:
```php
// src/Messages/SendEmailMessage.php
namespace GHI\Messages;

class SendEmailMessage
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $body,
    ) {}
}

// src/Handlers/SendEmailHandler.php
namespace GHI\Handlers;

use GHI\Messages\SendEmailMessage;
use Symfony\Component\Mailer\MailerInterface;

class SendEmailHandler
{
    public function __construct(
        private MailerInterface $mailer
    ) {}

    public function __invoke(SendEmailMessage $message): void
    {
        // Send email logic here
        $email = (new \Symfony\Component\Mime\Email())
            ->to($message->to)
            ->subject($message->subject)
            ->html($message->body);

        $this->mailer->send($email);
    }
}
```

**Usage**:
```php
use GHI\Services\MessageService;
use GHI\Messages\SendEmailMessage;

$messageService = new MessageService($bus);

// Dispatch email to queue (non-blocking)
$messageService->dispatch(new SendEmailMessage(
    'user@example.com',
    'Welcome!',
    'Thank you for joining...'
));

// Request returns immediately, email sent in background
```

---

## 📝 Integration Checklist

### Phase 1: Image Optimization (Week 1)
- [ ] Integrate `browser-image-compression` with FilePond
- [ ] Set up `intervention/image` service for server-side processing
- [ ] Create image upload endpoint with compression
- [ ] Test upload performance improvements

### Phase 2: Export Features (Week 2)
- [ ] Add Excel export to admin tables
- [ ] Add PDF export to admin tables
- [ ] Create PDF templates for reports
- [ ] Add CSV export functionality

### Phase 3: Security & Performance (Week 3)
- [ ] Implement rate limiting on API endpoints
- [ ] Set up message queue for async operations
- [ ] Configure queue workers
- [ ] Test rate limiting and queue processing

---

## 🔧 Configuration Files Needed

### Messenger Configuration
Create `config/messenger.php`:
```php
<?php

return [
    'default_bus' => 'messenger.bus.default',
    'buses' => [
        'messenger.bus.default' => [
            'middleware' => [],
        ],
    ],
    'transports' => [
        'async' => [
            'dsn' => 'doctrine://default',
        ],
    ],
    'routing' => [
        'GHI\Messages\SendEmailMessage' => 'async',
    ],
];
```

---

## 📚 Additional Resources

- [browser-image-compression Docs](https://github.com/Donaldcwl/browser-image-compression)
- [jsPDF Docs](https://github.com/parallax/jsPDF)
- [Intervention Image Docs](https://image.intervention.io/v3)
- [League CSV Docs](https://csv.thephpleague.com/)
- [Symfony Rate Limiter Docs](https://symfony.com/doc/current/rate_limiter.html)
- [Symfony Messenger Docs](https://symfony.com/doc/current/messenger.html)

---

**Document Version**: 1.0  
**Last Updated**: December 2024

