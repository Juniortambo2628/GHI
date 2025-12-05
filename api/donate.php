<?php
/**
 * Donations API Endpoint with Stripe Integration
 */

require_once __DIR__ . '/../../config/config.php';

use GHI\Models\Donation;
use GHI\Services\ValidationService;

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => ['message' => 'Method not allowed']]);
    exit;
}

try {
    // Get POST data
    $data = $_POST;

    // Validate required fields
    $errors = [];

    if (empty($data['firstname'])) {
        $errors['firstname'] = ['First name is required'];
    }

    if (empty($data['lastname'])) {
        $errors['lastname'] = ['Last name is required'];
    }

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = ['Valid email is required'];
    }

    // Determine amount
    $amount = 0;
    if (!empty($data['amount_preset']) && $data['amount_preset'] !== 'custom') {
        $amount = (float) $data['amount_preset'];
    } elseif (!empty($data['custom_amount'])) {
        $amount = (float) $data['custom_amount'];
    }

    if ($amount <= 0) {
        $errors['amount'] = ['Please select or enter a valid donation amount'];
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'errors' => $errors,
            'message' => 'Please correct the errors and try again',
        ]);
        exit;
    }

    // Create donation record
    $donationModel = new Donation();

    $donationData = [
        'firstname' => $data['firstname'],
        'lastname' => $data['lastname'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? null,
        'amount' => $amount,
        'donation_type' => $data['donation_type'] ?? 'one_time',
        'message' => $data['message'] ?? null,
        'status' => 'pending',
    ];

    $donationId = $donationModel->create($donationData);

    if (!$donationId) {
        throw new \Exception('Failed to create donation record');
    }

    // STRIPE INTEGRATION
    // Check if Stripe is configured
    $stripeSecretKey = getenv('STRIPE_SECRET_KEY');
    $useStripe = !empty($stripeSecretKey);

    if ($useStripe) {
        // Initialize Stripe (requires stripe-php library: composer require stripe/stripe-php)
        try {
            \Stripe\Stripe::setApiKey($stripeSecretKey);

            // Create Stripe Checkout Session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Donation to ' . SITE_NAME,
                            'description' => $donationData['message'] ?? 'Support our mission',
                        ],
                        'unit_amount' => (int)($amount * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => $donationData['donation_type'] === 'monthly' ? 'subscription' : 'payment',
                'success_url' => BASE_URL . '/donate-success.php?session_id={CHECKOUT_SESSION_ID}&donation_id=' . $donationId,
                'cancel_url' => BASE_URL . '/donate.php?canceled=1',
                'customer_email' => $donationData['email'],
                'metadata' => [
                    'donation_id' => $donationId,
                    'donor_name' => $donationData['firstname'] . ' ' . $donationData['lastname'],
                ],
            ]);

            // Update donation with Stripe session ID
            $donationModel->update($donationId, [
                'transaction_id' => $session->id,
                'payment_method' => 'stripe',
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Redirecting to secure payment...',
                'data' => [
                    'donation_id' => $donationId,
                    'amount' => $amount,
                    'type' => $donationData['donation_type'],
                    'stripe_session_id' => $session->id,
                    'stripe_url' => $session->url,
                ],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Stripe error - mark donation as failed
            $donationModel->updateStatus($donationId, 'failed');
            
            throw new \Exception('Payment processor error: ' . $e->getMessage());
        }
    } else {
        // Stripe not configured - return success without payment processing
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your donation! Payment processing is currently being set up.',
            'data' => [
                'donation_id' => $donationId,
                'amount' => $amount,
                'type' => $donationData['donation_type'],
                'note' => 'STRIPE_NOT_CONFIGURED: Add STRIPE_SECRET_KEY to .env file',
            ],
        ]);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => ['message' => 'An error occurred while processing your donation'],
    ]);

    // Log error
    if (function_exists('log_message')) {
        log_message($e->getMessage(), [], 'error');
    }
}

