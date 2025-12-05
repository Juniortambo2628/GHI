<?php

/**
 * Impact Activity View Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\ImpactActivity;
use GHI\Models\Event;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$impactModel = new ImpactActivity();
$eventModel = new Event();
$impact = null;

// Get impact ID
$impactId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load impact
if ($impactId > 0) {
    $impact = $impactModel->find($impactId);
    if ($impact === null || $impact === []) {
        header('Location: ' . BASE_URL . '/admin/impact-stories.php');
        exit;
    }

    // Load related event if exists
    $event = null;
    if (!empty($impact['event_id'])) {
        $event = $eventModel->find($impact['event_id']);
    }
} else {
    header('Location: ' . BASE_URL . '/admin/impact-stories.php');
    exit;
}

// Set page variables for components
$pageTitle = 'View Impact Story';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Impact Stories', 'url' => BASE_URL . '/admin/impact-stories.php'],
    ['label' => 'View Impact Story', 'url' => ''],
];
$backUrl = BASE_URL . '/admin/impact-stories.php';
$editUrl = BASE_URL . '/admin/impact-edit.php';
$deleteUrl = BASE_URL . '/admin/impact-delete.php';
$entityId = $impactId;
$entityName = 'impact story';
$status = $impact['status'];

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
                                <h5 class="mb-3"><?php echo e($impact['title']); ?></h5>

                                <?php
                                $imageUrl = empty($impact['thumbnail']) ? null : getImageUrl($impact['thumbnail']);
                                $altText = e($impact['title']);
                                include __DIR__ . '/includes/view-image-section.php';
                                ?>

                                <?php
                                $title = 'Description';
                                $content = $impact['description'] ?? 'No description available.';
                                include __DIR__ . '/includes/view-content-section.php';
                                ?>

                                <?php if ($event !== null && $event !== []) : ?>
                                    <?php
                                    $label = 'Related Event';
                                    $value = e($event['title']);
                                    $isLink = true;
                                    $linkUrl = BASE_URL . '/admin/event-view.php?id=' . $event['id'];
                                    include __DIR__ . '/includes/view-field-row.php';
                                    ?>
                                <?php endif;
                                ?>

                                <?php
                                $label = 'People Affected';
                                $value = '<strong class="text-primary">' . number_format($impact['people_affected'] ?? 0) . '</strong>';
                                $allowHtml = true;
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>

                                <?php
                                $label = 'Display Order';
                                $value = e($impact['display_order'] ?? 0);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>

                                <?php if (!empty($impact['outcome_summary'])) : ?>
                                    <?php
                                    $title = 'Outcome Summary';
                                    $content = nl2br(e($impact['outcome_summary']));
                                    include __DIR__ . '/includes/view-content-section.php';
                                    ?>
                                <?php endif;
                                ?>

                                <?php
                                $label = 'Created';
                                $value = formatDate($impact['created_at'], 'F j, Y g:i A');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>

                                <?php if (!empty($impact['updated_at']) && $impact['updated_at'] !== $impact['created_at']) : ?>
                                    <?php
                                    $label = 'Last Updated';
                                    $value = formatDate($impact['updated_at'], 'F j, Y g:i A');
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

