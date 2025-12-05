<?php
/**
 * Volunteer Application API Endpoint
 * Global Harmony Initiative Website
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

use GHI\Services\ValidationService;
use GHI\Services\CsrfService;
use GHI\Services\MailService;
use GHI\Models\ContactSubmission;
use GHI\Events\ContentCreatedEvent;

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
    ]);
    exit;
}

// Get request data
$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Validate CSRF token (check header first, then POST data)
// PHP converts headers: X-CSRF-Token becomes HTTP_X_CSRF_TOKEN
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $data[CSRF_TOKEN_NAME] ?? $data['_token'] ?? '';
if (! csrf_validate($token)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid security token. Please refresh the page and try again.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Validate input
$errors = [];

// Validate name (can be full name or split into firstname/lastname)
$name = trim($data['name'] ?? '');
$firstname = trim($data['firstname'] ?? '');
$lastname = trim($data['lastname'] ?? '');

if (empty($name) && empty($firstname)) {
    $errors['name'] = ['Name is required'];
} elseif (! empty($name)) {
    // Split name into first and last
    $nameParts = explode(' ', $name, 2);
    $firstname = $nameParts[0];
    $lastname = $nameParts[1] ?? '';
} elseif (empty($firstname)) {
    $errors['firstname'] = ['First name is required'];
}

// Validate email
$email = trim($data['email'] ?? '');
$emailErrors = ValidationService::validateEmail($email);
if (! empty($emailErrors)) {
    $errors['email'] = $emailErrors;
}

// Validate phone (optional)
$phone = trim($data['phone'] ?? '');

// Validate message
$message = trim($data['message'] ?? '');
$eventName = trim($data['event_name'] ?? '');

// Build message content
$messageContent = "Volunteer Application";
if ($eventName) {
    $messageContent .= "\n\nEvent: " . $eventName;
}
if ($phone) {
    $messageContent .= "\n\nPhone: " . $phone;
}
if ($message) {
    $messageContent .= "\n\nMessage: " . $message;
}

// If there are validation errors, return them
if (! empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors,
    ]);
    exit;
}

try {
    // Save to database as contact submission
    $contactModel = new ContactSubmission();
    $contactId = $contactModel->create([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'subject' => 'Volunteer Application' . ($eventName ? ' - ' . $eventName : ''),
        'message' => $messageContent,
        'status' => 'new',
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    // Send email notification
    $emailSent = MailService::sendContactForm(
        $firstname . ' ' . $lastname,
        $email,
        'Volunteer Application' . ($eventName ? ' - ' . $eventName : ''),
        $messageContent
    );

    // Dispatch event for content creation
    $event = new ContentCreatedEvent('volunteer_application', $contactId, [
        'email' => $email,
        'event_name' => $eventName,
    ]);
    event_dispatch($event, ContentCreatedEvent::NAME);

    // Log the submission
    log_message('info', 'Volunteer application received', [
        'contact_id' => $contactId,
        'email' => $email,
        'event_name' => $eventName,
    ]);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your interest in volunteering! We will contact you soon.',
        'data' => [
            'id' => $contactId,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Exception $e) {
    // Log error
    log_message('error', 'Volunteer application failed', [
        'error' => $e->getMessage(),
        'email' => $email ?? 'unknown',
    ]);

    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your application. Please try again later.',
    ]);
}

