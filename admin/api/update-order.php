<?php
/**
 * Update Display Order API Endpoint
 * Handles bulk update of display_order for draggable sorting
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/cache-helper.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Rate limiting for order updates (30 per minute)
require_once __DIR__ . '/../../vendor/autoload.php';
use GHI\Services\RateLimitService;

if (!RateLimitService::checkAndRespond('order-update', [
    'limit' => 30,
    'interval' => '1 minute',
    'amount' => 30,
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
$input = json_decode(file_get_contents('php://input'), true);
$token = $input['_token'] ?? $_POST['_token'] ?? '';
if (!csrf_validate($token)) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Get data
$entityType = trim((string) ($input['entity_type'] ?? $_POST['entity_type'] ?? ''));
$items = $input['items'] ?? $_POST['items'] ?? [];

if ($entityType === '' || $entityType === '0' || !is_array($items) || $items === []) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Validate entity type
$allowedTypes = ['causes', 'impact_activities'];
if (!in_array($entityType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid entity type']);
    exit;
}

try {
    $db = get_db();
    $db->beginTransaction();

    // Update each item's display_order
    foreach ($items as $item) {
        $id = (int)($item['id'] ?? 0);
        $order = (int)($item['order'] ?? 0);
        if ($id <= 0) {
            continue;
        }

        if ($order <= 0) {
            continue;
        }

        $db->executeStatement(
            sprintf('UPDATE %s SET display_order = ? WHERE id = ?', $entityType),
            [$order, $id]
        );
    }

    $db->commit();

    // Clear cache
    clear_cache();

    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully',
        'updated_count' => count($items)
    ]);
} catch (\Exception $exception) {
    if ($db->isTransactionActive()) {
        $db->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update order: ' . $exception->getMessage()
    ]);
}

