<?php
/**
 * Story Save API Endpoint
 * Handles AJAX form submission
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/cache-helper.php';

use GHI\Models\Story;
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
$storyId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$author = trim($_POST['author'] ?? '');
$category = trim($_POST['category'] ?? '');
$status = trim($_POST['status'] ?? 'draft');
$slug = trim($_POST['slug'] ?? '');
$featuredImage = trim($_POST['featured_image'] ?? '');

$errors = [];

// Validate
if ($title === '' || $title === '0') {
    $errors['title'] = 'Title is required';
} elseif (strlen($title) < 3) {
    $errors['title'] = 'Title must be at least 3 characters';
}

if ($content === '' || $content === '0') {
    $errors['content'] = 'Content is required';
} elseif (strlen($content) < 10) {
    $errors['content'] = 'Content must be at least 10 characters';
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
    $storyModel = new Story();
    
    $data = [
        'title' => $title,
        'content' => $content,
        'author' => $author,
        'category' => $category,
        'status' => $status,
        'slug' => $slug,
        'featured_image' => $featuredImage,
    ];
    
    if ($storyId > 0) {
        // Update existing
        $storyModel->update($storyId, $data);
        $message = 'Story updated successfully!';
        log_message('info', 'Story updated via modal', ['story_id' => $storyId]);
    } else {
        // Create new
        $storyId = $storyModel->create($data);
        $message = 'Story created successfully!';
        
        // Dispatch event
        $event = new ContentCreatedEvent('story', $storyId, ['title' => $title]);
        event_dispatch($event, ContentCreatedEvent::NAME);
        
        log_message('info', 'Story created via modal', ['story_id' => $storyId]);
    }
    
    // Clear cache
    SimpleCache::clear();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => array_merge(['id' => $storyId], $data)
    ]);
    
} catch (\Exception $exception) {
    log_message('error', 'Story save failed via modal', ['error' => $exception->getMessage()]);
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $exception->getMessage()
    ]);
}

