<?php
/**
 * Clear Cache Utility
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/cache-helper.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

// Clear cache
$success = SimpleCache::clear();

// Set page variables
$pageTitle = 'Clear Cache';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Clear Cache', 'url' => BASE_URL . '/admin/clear-cache.php'],
];

// Include header
require_once __DIR__ . '/includes/header.php';

// Include sidebar
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <!-- Hero Area -->
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Clear Cache</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($success): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Cache cleared successfully! All cached data has been removed.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Failed to clear cache. Please check file permissions.
                                </div>
                            <?php endif;
 ?>
                            
                            <p class="mt-3">
                                <a href="<?php echo BASE_URL; ?>/admin/index.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

