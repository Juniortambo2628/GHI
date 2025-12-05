<?php
/**
 * Generic File Upload API Endpoint
 * Global Harmony Initiative Website
 * Handles generic file uploads for FilePond
 */

// FilePond expects text/plain response with server ID
// But we'll use JSON for error responses
header('Content-Type: text/plain');

require_once __DIR__ . '/../config/config.php';

use GHI\Services\FileService;
use GHI\Services\CsrfService;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Validate CSRF token (check header first, then POST data)
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST[CSRF_TOKEN_NAME] ?? $_POST['_token'] ?? '';
    if (! csrf_validate($token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid security token. Please refresh the page and try again.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Check if file was uploaded
    // FilePond may send file as 'filepond' or 'file'
    if (isset($_FILES['filepond'])) {
        $fileKey = 'filepond';
    } elseif (isset($_FILES['file'])) {
        $fileKey = 'file';
    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $file = $_FILES[$fileKey];

    // Validate file upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File size exceeds server maximum',
            UPLOAD_ERR_FORM_SIZE => 'File size exceeds form maximum',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
        ];
        
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $errorMessages[$file['error']] ?? 'File upload error',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Get allowed types from config or request
    $allowedTypes = ! empty($_POST['allowed_types']) 
        ? explode(',', $_POST['allowed_types'])
        : explode(',', UPLOADS_ALLOWED_TYPES ?? 'jpg,jpeg,png,gif,pdf,doc,docx');
    
    $allowedTypes = array_map('trim', $allowedTypes);
    $allowedTypes = array_map('strtolower', $allowedTypes);

    // Validate file type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (! in_array($fileExtension, $allowedTypes, true)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Validate file size
    $maxSize = ! empty($_POST['max_size']) 
        ? (int)$_POST['max_size']
        : (UPLOADS_MAX_SIZE ?? 10 * 1024 * 1024); // Default: 10MB
    
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'File size exceeds maximum allowed size (' . round($maxSize / 1024 / 1024, 2) . 'MB)',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Determine upload path based on file type or request parameter
    $uploadSubdirectory = $_POST['subdirectory'] ?? 'files';
    $uploadPath = 'uploads/' . $uploadSubdirectory . '/' . date('Y/m');

    // Upload file using FileService
    $result = FileService::upload($file, $uploadPath, $allowedTypes);

    if (! $result['success']) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $result['error'] ?? 'Failed to upload file',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Log the upload
    log_message('info', 'File uploaded successfully', [
        'filename' => $result['filename'],
        'path' => $result['path'],
        'size' => $result['size'],
        'type' => $uploadSubdirectory,
    ]);

    // Return FilePond-compatible response
    // FilePond expects the server ID as the response body
    // We'll return the file path as the server ID
    $serverId = $result['path'];
    
    http_response_code(200);
    // Return just the server ID as plain text (FilePond's default expectation)
    echo $serverId;
} catch (\Exception $e) {
    // Log error
    log_message('error', 'File upload failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);

    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while uploading the file. Please try again.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

