<?php
/**
 * Event View Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Event;
use GHI\Models\Initiative;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$eventModel = new Event();
$initiativeModel = new Initiative();
$event = null;

// Get event ID
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load event
if ($eventId > 0) {
    $event = $eventModel->find($eventId);
    if ($event === null || $event === []) {
        header('Location: ' . BASE_URL . '/admin/events.php');
        exit;
    }
    
    // Load related initiative if exists
    $initiative = null;
    if (!empty($event['initiative_id'])) {
        $initiative = $initiativeModel->find($event['initiative_id']);
    }
} else {
    header('Location: ' . BASE_URL . '/admin/events.php');
    exit;
}

// Format event date and time
$eventDate = empty($event['event_date']) ? 'N/A' : formatDate($event['event_date'], 'F j, Y');
$eventTime = empty($event['event_date']) ? 'N/A' : date('g:i A', strtotime((string) $event['event_date']));
$eventDateTime = empty($event['event_date']) ? 'N/A' : formatDate($event['event_date'], 'F j, Y g:i A');

// Set page variables for components
$pageTitle = 'View Event';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Events', 'url' => BASE_URL . '/admin/events.php'],
    ['label' => 'View Event', 'url' => ''],
];
$backUrl = BASE_URL . '/admin/events.php';
$editUrl = BASE_URL . '/admin/event-edit.php';
$deleteUrl = BASE_URL . '/admin/event-delete.php';
$entityId = $eventId;
$entityName = 'event';
$status = $event['status'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    <main class="admin-main">
        <div class="container-fluid">
            <div class="card">
                <?php include __DIR__ . '/includes/view-page-header.php'; ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="mb-3"><?php echo e($event['title']); ?></h5>
                                
                                <?php
                                $imageUrl = empty($event['image']) ? null : getImageUrl($event['image']);
                                $altText = e($event['title']);
                                include __DIR__ . '/includes/view-image-section.php';
                                ?>
                                
                                <?php
                                $title = 'Description';
                                $content = $event['description'] ?? 'No description available.';
                                include __DIR__ . '/includes/view-content-section.php';
                                ?>
                                
                                <?php
                                $label = 'Event Date';
                                $value = e($eventDate);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php
                                $label = 'Event Time';
                                $value = e($eventTime);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php
                                $label = 'Location';
                                $value = e($event['location'] ?? 'N/A');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if ($initiative !== null && $initiative !== []): ?>
                                    <?php
                                    $label = 'Initiative';
                                    $value = e($initiative['title']);
                                    $isLink = true;
                                    $linkUrl = BASE_URL . '/admin/initiative-view.php?id=' . $initiative['id'];
                                    include __DIR__ . '/includes/view-field-row.php';
                                    ?>
                                <?php endif;
                                 ?>
                                
                                <?php
                                $label = 'Created';
                                $value = formatDate($event['created_at'], 'F j, Y g:i A');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if (!empty($event['updated_at']) && $event['updated_at'] !== $event['created_at']): ?>
                                    <?php
                                    $label = 'Last Updated';
                                    $value = formatDate($event['updated_at'], 'F j, Y g:i A');
                                    include __DIR__ . '/includes/view-field-row.php';
                                    ?>
                                <?php endif;
                                 ?>
                            </div>
                        </div>
                        
                        <?php include __DIR__ . '/includes/view-page-sidebar.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

