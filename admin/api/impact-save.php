<?php
/**
 * Impact Activity Save API Endpoint
 * Handles AJAX form submission
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/cache-helper.php';

use GHI\Models\ImpactActivity;
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
$activityId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$metricType = trim($_POST['metric_type'] ?? '');
$metricValue = empty($_POST['metric_value']) ? 0 : (float)$_POST['metric_value'];
$activityDate = trim($_POST['activity_date'] ?? date('Y-m-d'));
$location = trim($_POST['location'] ?? '');
$status = trim($_POST['status'] ?? 'draft');
$featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
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
    $activityModel = new ImpactActivity();
    
    $data = [
        'title' => $title,
        'description' => $description,
        'metric_type' => $metricType,
        'metric_value' => $metricValue,
        'activity_date' => $activityDate,
        'location' => $location,
        'status' => $status,
        'featured' => $featured,
        'image' => $image,
    ];
    
    if ($activityId > 0) {
        // Update existing
        $activityModel->update($activityId, $data);
        $message = 'Impact Activity updated successfully!';
        log_message('info', 'Impact Activity updated via modal', ['activity_id' => $activityId]);
    } else {
        // Create new
        $activityId = $activityModel->create($data);
        $message = 'Impact Activity created successfully!';
        
        // Dispatch event
        $event = new ContentCreatedEvent('impact_activity', $activityId, ['title' => $title]);
        event_dispatch($event, ContentCreatedEvent::NAME);
        
        log_message('info', 'Impact Activity created via modal', ['activity_id' => $activityId]);
    }
    
    // Clear cache
    SimpleCache::clear();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => array_merge(['id' => $activityId], $data)
    ]);
    
} catch (\Exception $exception) {
    log_message('error', 'Impact Activity save failed via modal', ['error' => $exception->getMessage()]);
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $exception->getMessage()
    ]);
}

