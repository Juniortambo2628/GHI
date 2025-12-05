<?php
/**
 * Newsletter Subscription API Endpoint
 * Global Harmony Initiative Website
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

use GHI\Services\ValidationService;
use GHI\Services\CsrfService;
use GHI\Services\MailService;
use GHI\Models\NewsletterSubscriber;
use GHI\Services\DatabaseService;
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

// Validate email
$email = trim($data['email'] ?? '');
$emailErrors = ValidationService::validateEmail($email);
if (! empty($emailErrors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => ['email' => $emailErrors],
    ]);
    exit;
}

try {
    // Check if subscriber already exists
    $subscriberModel = new NewsletterSubscriber();
    $connection = DatabaseService::getConnection();
    
    // Check if subscriber already exists
    $existing = $connection->fetchAssociative(
        'SELECT * FROM newsletter_subscribers WHERE email = ?',
        [$email]
    );
    
    if ($existing) {
        // Update existing subscriber
        $connection->update(
            'newsletter_subscribers',
            ['status' => 'active', 'updated_at' => date('Y-m-d H:i:s')],
            ['id' => $existing['id']]
        );
        $subscriberId = $existing['id'];
    } else {
        // Create new subscriber
        $subscriberData = [
            'email' => $email,
            'status' => 'active',
        ];
        $connection->insert('newsletter_subscribers', $subscriberData);
        $subscriberId = (int)$connection->lastInsertId();
    }
    
    // Get subscriber
    $subscriber = $connection->fetchAssociative(
        'SELECT * FROM newsletter_subscribers WHERE id = ?',
        [$subscriberId]
    );

    // Send confirmation email
    $emailSent = MailService::sendNewsletterConfirmation($email);

    // Dispatch event for content creation
    if (isset($subscriber['id'])) {
        $event = new ContentCreatedEvent('newsletter_subscription', $subscriber['id'], [
            'email' => $email,
        ]);
        event_dispatch($event, ContentCreatedEvent::NAME);
    }

    // Log the subscription
    log_message('info', 'Newsletter subscription', [
        'email' => $email,
        'subscriber_id' => $subscriber['id'] ?? null,
    ]);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for subscribing!',
        'data' => [
            'email' => $email,
            'id' => $subscriber['id'] ?? null,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Exception $e) {
    // Log error
    log_message('error', 'Newsletter subscription failed', [
        'error' => $e->getMessage(),
        'email' => $email ?? 'unknown',
    ]);

    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your subscription. Please try again later.',
    ]);
}

