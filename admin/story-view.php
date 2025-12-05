<?php
/**
 * Story View Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Story;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$storyModel = new Story();
$story = null;

// Get story ID
$storyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load story
if ($storyId > 0) {
    $story = $storyModel->find($storyId);
    if ($story === null || $story === []) {
        header('Location: ' . BASE_URL . '/admin/stories.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . '/admin/stories.php');
    exit;
}

// Set page variables for components
$pageTitle = 'View Story';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Stories', 'url' => BASE_URL . '/admin/stories.php'],
    ['label' => 'View Story', 'url' => ''],
];
$backUrl = BASE_URL . '/admin/stories.php';
$editUrl = BASE_URL . '/admin/story-edit.php';
$deleteUrl = BASE_URL . '/admin/story-delete.php';
$entityId = $storyId;
$entityName = 'story';
$status = $story['status'];

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
                                <h5 class="mb-3"><?php echo e($story['title']); ?></h5>
                                
                                <?php
                                $imageUrl = empty($story['image']) ? null : getImageUrl($story['image']);
                                $altText = e($story['title']);
                                include __DIR__ . '/includes/view-image-section.php';
                                ?>
                                
                                <?php
                                $title = 'Description';
                                $content = $story['description'] ?? 'No description available.';
                                include __DIR__ . '/includes/view-content-section.php';
                                ?>
                                
                                <?php if (!empty($story['content'])): ?>
                                    <?php
                                    $title = 'Content';
                                    $content = $story['content'];
                                    include __DIR__ . '/includes/view-content-section.php';
                                    ?>
                                <?php endif;
                                 ?>
                                
                                <?php
                                $label = 'Category';
                                $value = ucfirst((string) $story['category']);
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if (!empty($story['author'])): ?>
                                    <?php
                                    $label = 'Author';
                                    $value = e($story['author']);
                                    include __DIR__ . '/includes/view-field-row.php';
                                    ?>
                                <?php endif;
                                 ?>
                                
                                <?php
                                $label = 'Created';
                                $value = formatDate($story['created_at'], 'F j, Y g:i A');
                                include __DIR__ . '/includes/view-field-row.php';
                                ?>
                                
                                <?php if (!empty($story['updated_at']) && $story['updated_at'] !== $story['created_at']): ?>
                                    <?php
                                    $label = 'Last Updated';
                                    $value = formatDate($story['updated_at'], 'F j, Y g:i A');
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

