<?php
/**
 * Initiative Save API Endpoint
 * Handles AJAX form submission
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/cache-helper.php';

use GHI\Models\Initiative;
use GHI\Events\ContentCreatedEvent;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Rate limiting for form submissions (20 per minute)
require_once __DIR__ . '/../../vendor/autoload.php';
use GHI\Services\RateLimitService;

if (!RateLimitService::checkAndRespond('form', [
    'limit' => 20,
    'interval' => '1 minute',
    'amount' => 20,
])) {
    exit; // Response already sent
}

// Set JSON header
header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate CSRF token
$token = $_POST[CSRF_TOKEN_NAME] ?? $_POST['_token'] ?? '';
if (!csrf_validate($token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Get form data
$initiativeId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$causeId = empty($_POST['cause_id']) ? null : (int)$_POST['cause_id'];
$status = trim($_POST['status'] ?? 'draft');
$image = trim($_POST['image'] ?? '');

$errors = [];

// Validate
if ($title === '' || $title === '0') {
    $errors['title'] = 'Title is required';
} elseif (strlen($title) < 3) {
    $errors['title'] = 'Title must be at least 3 characters';
}

if ($description === '' || $description === '0') {
    $errors['description'] = 'Description is required';
} elseif (strlen($description) < 10) {
    $errors['description'] = 'Description must be at least 10 characters';
}

if ($category === '' || $category === '0') {
    $errors['category'] = 'Category is required';
}

// If errors, return them
if ($errors !== []) {
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

// Save to database
try {
    $initiativeModel = new Initiative();
    
    $data = [
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'cause_id' => $causeId,
        'status' => $status,
        'image' => $image,
    ];
    
    if ($initiativeId > 0) {
        // Update existing
        $initiativeModel->update($initiativeId, $data);
        $message = 'Initiative updated successfully!';
        log_message('info', 'Initiative updated via modal', ['initiative_id' => $initiativeId]);
    } else {
        // Create new
        $initiativeId = $initiativeModel->create($data);
        $message = 'Initiative created successfully!';
        
        // Dispatch event
        $event = new ContentCreatedEvent('initiative', $initiativeId, ['title' => $title]);
        event_dispatch($event, ContentCreatedEvent::NAME);
        
        log_message('info', 'Initiative created via modal', ['initiative_id' => $initiativeId]);
    }
    
    // Clear cache
    SimpleCache::clear();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => array_merge(['id' => $initiativeId], $data)
    ]);
    
} catch (\Exception $exception) {
    log_message('error', 'Initiative save failed via modal', ['error' => $exception->getMessage()]);
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $exception->getMessage()
    ]);
}

