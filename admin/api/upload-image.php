<?php
/**
 * Image Upload API Endpoint
 * Handles FilePond file uploads
 */

require_once __DIR__ . '/../../config/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Rate limiting for uploads (10 uploads per minute)
require_once __DIR__ . '/../../vendor/autoload.php';
use GHI\Services\RateLimitService;

if (!RateLimitService::checkAndRespond('upload', [
    'limit' => 10,
    'interval' => '1 minute',
    'amount' => 10,
])) {
    exit; // Response already sent
}

// Set JSON header
header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['filepond']) || $_FILES['filepond']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['filepond'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, and WebP images are allowed.']);
    exit;
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Maximum size is 5MB.']);
    exit;
}

// Generate unique filename
$extension = pathinfo((string) $file['name'], PATHINFO_EXTENSION);
$filename = 'upload-' . time() . '-' . bin2hex(random_bytes(8)) . '.' . $extension;

// Upload directory
$uploadDir = __DIR__ . '/../../uploads/images/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create upload directory']);
    exit;
}

// Move uploaded file
$destination = $uploadDir . $filename;
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to move uploaded file']);
    exit;
}

// Process image (resize and optimize) if ImageService is available
$originalSize = $file['size'];
$processedSize = $originalSize;

try {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $imageService = new \GHI\Services\ImageService();
    
    $relativePath = 'uploads/images/' . $filename;
    $result = $imageService->processUploadedImage($relativePath, [
        'maxWidth' => 1920,
        'maxHeight' => 1080,
        'quality' => 85,
        'createThumbnail' => true,
        'thumbnailSize' => 300,
    ]);
    
    if ($result['success']) {
        $processedSize = $result['processedSize'] ?? $originalSize;
        $filename = basename($result['processed'] ?? $relativePath);
    }
} catch (\Exception $exception) {
    // If ImageService fails, continue with original file
    log_message('warning', 'Image processing skipped', [
        'error' => $exception->getMessage(),
        'filename' => $filename
    ]);
}

// Log upload
log_message('info', 'Image uploaded via FilePond', [
    'filename' => $filename,
    'original_size' => $originalSize,
    'processed_size' => $processedSize,
    'type' => $mimeType,
    'user' => $_SESSION['admin_user_id'] ?? 'unknown'
]);

// Return success with filename
// FilePond expects the serverId to be returned
echo json_encode([
    'success' => true,
    'filename' => $filename,
    'path' => '/uploads/images/' . $filename,
    'size' => $processedSize,
    'original_size' => $originalSize,
    'compression' => $originalSize > 0 ? round((1 - $processedSize / $originalSize) * 100, 1) : 0
]);

