<?php
/**
 * Event Save API Endpoint
 * Handles AJAX form submission
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/cache-helper.php';

use GHI\Models\Event;
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
$eventId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$eventDate = trim($_POST['event_date'] ?? '');
$location = trim($_POST['location'] ?? '');
$status = trim($_POST['status'] ?? 'upcoming');
$slug = trim($_POST['slug'] ?? '');
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

if ($eventDate === '' || $eventDate === '0') {
    $errors['event_date'] = 'Event date is required';
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

// Auto-generate slug if empty
if ($slug === '' || $slug === '0') {
    $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
}

// Save to database
try {
    $eventModel = new Event();
    
    $data = [
        'title' => $title,
        'description' => $description,
        'event_date' => $eventDate,
        'location' => $location,
        'status' => $status,
        'slug' => $slug,
        'image' => $image,
    ];
    
    if ($eventId > 0) {
        // Update existing
        $eventModel->update($eventId, $data);
        $message = 'Event updated successfully!';
        log_message('info', 'Event updated via modal', ['event_id' => $eventId]);
    } else {
        // Create new
        $eventId = $eventModel->create($data);
        $message = 'Event created successfully!';
        
        // Dispatch event
        $event = new ContentCreatedEvent('event', $eventId, ['title' => $title]);
        event_dispatch($event, ContentCreatedEvent::NAME);
        
        log_message('info', 'Event created via modal', ['event_id' => $eventId]);
    }
    
    // Clear cache
    SimpleCache::clear();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => array_merge(['id' => $eventId], $data)
    ]);
    
} catch (\Exception $exception) {
    log_message('error', 'Event save failed via modal', ['error' => $exception->getMessage()]);
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $exception->getMessage()
    ]);
}

