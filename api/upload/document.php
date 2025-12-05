<?php
/**
 * Document Upload API Endpoint
 * Global Harmony Initiative Website
 * Handles document uploads for FilePond
 */

// FilePond expects text/plain response with server ID
// But we'll use JSON for error responses
header('Content-Type: text/plain');

require_once __DIR__ . '/../../config/config.php';

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

    // Validate file type (documents only)
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (! in_array($fileExtension, $allowedTypes, true)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only documents (pdf, doc, docx, txt, rtf) are allowed.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Validate file size (max 10MB for documents)
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'File size exceeds maximum allowed size (10MB)',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Validate MIME type
    $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'application/rtf',
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (! in_array($mimeType, $allowedMimeTypes, true)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. File does not appear to be a valid document.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Upload file using FileService
    $uploadPath = 'uploads/documents/' . date('Y/m');
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
    log_message('info', 'Document uploaded successfully', [
        'filename' => $result['filename'],
        'path' => $result['path'],
        'size' => $result['size'],
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
    log_message('error', 'Document upload failed', [
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

