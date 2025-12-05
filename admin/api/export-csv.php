<?php
/**
 * CSV Export API Endpoint
 * Handles server-side CSV export for admin tables
 */

require_once __DIR__ . '/../../config/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Rate limiting for exports (10 per minute)
require_once __DIR__ . '/../../vendor/autoload.php';
use GHI\Services\RateLimitService;

if (!RateLimitService::checkAndRespond('export', [
    'limit' => 10,
    'interval' => '1 minute',
    'amount' => 10,
])) {
    exit; // Response already sent
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['data']) || !is_array($input['data'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

$data = $input['data'];
$filename = $input['filename'] ?? 'export.csv';

// Use CsvService to generate CSV
use GHI\Services\CsvService;

try {
    $csvService = new CsvService();
    $csvService->download($data, $filename);
} catch (\Exception $exception) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate CSV: ' . $exception->getMessage()]);
    exit;
}

