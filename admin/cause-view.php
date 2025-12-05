<?php
/**
 * Cause View Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Cause;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$causeModel = new Cause();
$cause = null;

// Get cause ID
$causeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load cause
if ($causeId > 0) {
    $cause = $causeModel->find($causeId);
    if ($cause === null || $cause === []) {
        header('Location: ' . BASE_URL . '/admin/causes.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . '/admin/causes.php');
    exit;
}

// Set page variables for components
$pageTitle = 'View Cause';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Causes', 'url' => BASE_URL . '/admin/causes.php'],
    ['label' => 'View Cause', 'url' => ''],
];
$backUrl = BASE_URL . '/admin/causes.php';
$editUrl = BASE_URL . '/admin/cause-edit.php';
$deleteUrl = BASE_URL . '/admin/cause-delete.php';
$entityId = $causeId;
$entityName = 'cause';
$status = $cause['status'];

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
                                <h5 class="mb-3"><?php echo e($cause['title']); ?></h5>
                                
                                <?php
                                $imageUrl = empty($cause['image']) ? null : getImageUrl($cause['image']);
                                $altText = e($cause['title']);
                                include __DIR__ . '/includes/view-image-section.php';
                                ?>
                                
                                <?php
                                $title = 'Description';
                                $content = $cause['description'];
                                include __DIR__ . '/includes/view-content-section.php';
                                ?>
                                
                                <?php
                                $label = 'Slug';
                                $value = e($cause['slug']);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php
                                $label = 'Display Order';
                                $value = e($cause['display_order'] ?? 0);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php
                                $label = 'Created';
                                $value = formatDate($cause['created_at'], 'F j, Y g:i A');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if (!empty($cause['updated_at']) && $cause['updated_at'] !== $cause['created_at']): ?>
                                    <?php
                                    $label = 'Last Updated';
                                    $value = formatDate($cause['updated_at'], 'F j, Y g:i A');
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

