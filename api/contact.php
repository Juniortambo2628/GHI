<?php
/**
 * Contact Form API Endpoint
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

// Validate firstname
$firstname = trim($data['firstname'] ?? $data['name'] ?? '');
if (empty($firstname)) {
    $errors['firstname'] = ['First name is required'];
} elseif (strlen($firstname) < 2) {
    $errors['firstname'] = ['First name must be at least 2 characters'];
}

// Validate lastname (optional if name is provided)
$lastname = trim($data['lastname'] ?? '');
if (empty($firstname) && empty($lastname) && ! empty($data['name'])) {
    // If only 'name' is provided, split it
    $nameParts = explode(' ', $data['name'], 2);
    $firstname = $nameParts[0];
    $lastname = $nameParts[1] ?? '';
}

// Validate email
$email = trim($data['email'] ?? '');
$emailErrors = ValidationService::validateEmail($email);
if (! empty($emailErrors)) {
    $errors['email'] = $emailErrors;
}

// Validate message
$message = trim($data['message'] ?? '');
$messageErrors = ValidationService::validateLength($message, 10, 5000, 'Message');
if (! empty($messageErrors)) {
    $errors['message'] = $messageErrors;
}

// Validate subject (optional)
$subject = trim($data['subject'] ?? 'Contact Form Submission');

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
    // Save to database
    $contactModel = new ContactSubmission();
    $contactData = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'status' => 'new',
    ];
    
    // Only add created_at if column exists
    try {
        $contactId = $contactModel->create($contactData);
    } catch (\Exception $e) {
        // If created_at doesn't exist, try without it
        if (strpos($e->getMessage(), 'created_at') !== false) {
            unset($contactData['created_at']);
            $contactId = $contactModel->create($contactData);
        } else {
            throw $e;
        }
    }

    // Send email notification
    $emailSent = MailService::sendContactForm(
        $firstname . ' ' . $lastname,
        $email,
        $subject,
        $message
    );

    // Dispatch event for content creation
    $event = new ContentCreatedEvent('contact_submission', $contactId, [
        'email' => $email,
        'subject' => $subject,
    ]);
    event_dispatch($event, ContentCreatedEvent::NAME);

    // Log the submission
    log_message('info', 'Contact form submission received', [
        'contact_id' => $contactId,
        'email' => $email,
        'subject' => $subject,
    ]);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message. We will get back to you soon.',
        'data' => [
            'id' => $contactId,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Exception $e) {
    // Log error
    log_message('error', 'Contact form submission failed', [
        'error' => $e->getMessage(),
        'email' => $email ?? 'unknown',
    ]);

    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request. Please try again later.',
    ]);
}

