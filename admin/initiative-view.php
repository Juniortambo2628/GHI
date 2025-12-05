<?php
/**
 * Initiative View Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Initiative;
use GHI\Models\Cause;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$initiativeModel = new Initiative();
$causeModel = new Cause();
$initiative = null;

// Get initiative ID
$initiativeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load initiative
if ($initiativeId > 0) {
    $initiative = $initiativeModel->find($initiativeId);
    if ($initiative === null || $initiative === []) {
        header('Location: ' . BASE_URL . '/admin/initiatives.php');
        exit;
    }
    
    // Load related cause if exists
    $cause = null;
    if (!empty($initiative['cause_id'])) {
        $cause = $causeModel->find($initiative['cause_id']);
    }
} else {
    header('Location: ' . BASE_URL . '/admin/initiatives.php');
    exit;
}

// Category to objective mapping
$categoryToObjective = [
    'education' => 'Access & Youth Development',
    'health' => 'Well-being',
    'livelihood' => 'Poverty Alleviation',
    'empowerment' => 'Community',
    'partnerships' => 'Global Awareness',
];

// Set page variables for components
$pageTitle = 'View Initiative';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Initiatives', 'url' => BASE_URL . '/admin/initiatives.php'],
    ['label' => 'View Initiative', 'url' => ''],
];
$backUrl = BASE_URL . '/admin/initiatives.php';
$editUrl = BASE_URL . '/admin/initiative-edit.php';
$deleteUrl = BASE_URL . '/admin/initiative-delete.php';
$entityId = $initiativeId;
$entityName = 'initiative';
$status = $initiative['status'];

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
                                <h5 class="mb-3"><?php echo e($initiative['title']); ?></h5>
                                
                                <?php
                                $imageUrl = empty($initiative['image']) ? null : getImageUrl($initiative['image']);
                                $altText = e($initiative['title']);
                                include __DIR__ . '/includes/view-image-section.php';
                                ?>
                                
                                <?php
                                $title = 'Description';
                                $content = $initiative['description'] ?? 'No description available.';
                                include __DIR__ . '/includes/view-content-section.php';
                                ?>
                                
                                <?php if (!empty($initiative['content'])): ?>
                                    <?php
                                    $title = 'Content';
                                    $content = $initiative['content'];
                                    include __DIR__ . '/includes/view-content-section.php';
                                    ?>
                                <?php endif;
                                 ?>
                                
                                <?php
                                $label = 'Category';
                                $value = ucfirst((string) $initiative['category']);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php
                                $label = 'Core Objective';
                                $value = e($categoryToObjective[$initiative['category']] ?? 'Community Development');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if ($cause !== null && $cause !== []): ?>
                                    <?php
                                    $label = 'Related Cause';
                                    $value = e($cause['title']);
                                    $isLink = true;
                                    $linkUrl = BASE_URL . '/admin/cause-view.php?id=' . $cause['id'];
                                    include __DIR__ . '/includes/view-field-row.php';
                                    ?>
                                <?php endif;
                                 ?>
                                
                                <?php
                                $label = 'Created';
                                $value = formatDate($initiative['created_at'], 'F j, Y g:i A');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if (!empty($initiative['updated_at']) && $initiative['updated_at'] !== $initiative['created_at']): ?>
                                    <?php
                                    $label = 'Last Updated';
                                    $value = formatDate($initiative['updated_at'], 'F j, Y g:i A');
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

