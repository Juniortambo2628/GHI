<?php
/**
 * Admin Header Component
 * Global Harmony Initiative Admin Dashboard
 */

// Load configuration if not already loaded
if (! defined('SITE_NAME')) {
    require_once __DIR__ . '/../../config/config.php';
}

use GHI\Services\AuthService;

// Enforce authentication for all admin pages using the shared header include
require_once __DIR__ . '/auth-check.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check if user is logged in using AuthService
$isLoggedIn = AuthService::isLoggedIn();
$adminEmail = AuthService::getEmail() ?? 'admin@example.com';
$adminName = $adminEmail; // Can be enhanced to get name from database
$nameParts = explode('@', $adminEmail);
$adminInitials = strtoupper(substr($nameParts[0], 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) : 'Admin Dashboard - ' . SITE_NAME; ?></title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/Logo/Square-White-BG.png">
    <?php if (defined('SENTRY_DSN') && SENTRY_DSN): ?>
    <meta name="sentry-dsn" content="<?php echo e(SENTRY_DSN); ?>">
    <?php endif;
 ?>
    <meta name="app-env" content="<?php echo e(ENVIRONMENT); ?>">
    
    <!-- Bootstrap 5 CSS (Local) -->
    <link href="<?php echo BASE_URL; ?>/admin/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons (Local) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/admin/css/bootstrap-icons.min.css">
    
    <!-- Font Awesome (Local) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/lib/fontawesome/css/all.min.css">
    
    <!-- Admin Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/admin/css/admin.css" rel="stylesheet">
    <!-- MicroModal CSS -->
    <link href="<?php echo BASE_URL; ?>/admin/css/micromodal.css" rel="stylesheet">
    <!-- Vendor CSS (Tabulator, FilePond, Quill) -->
    <?php 
    $vendorCssFiles = [
        'vendor-tabulator-*.css',
        'vendor-filepond-*.css',
        'vendor-quill-*.css'
    ];
    
    foreach ($vendorCssFiles as $pattern):
        $cssFile = get_vite_asset($pattern);
        if ($cssFile): 
    ?>
    <link href="<?php echo $cssFile; ?>" rel="stylesheet">
    <?php 
        endif;
    endforeach;
    ?>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container-fluid d-flex align-items-center">
            <!-- Sidebar Toggle (Mobile) -->
            <button class="btn btn-link text-dark me-3 d-lg-none" id="sidebarToggle" type="button">
                <i class="bi bi-list fs-3"></i>
            </button>
            
            <!-- Logo and Brand -->
            <div class="header-logo me-3">
                <div class="logo-image">
                    <img src="<?php echo LOGO_URL; ?>/Square-White-BG.png" alt="<?php echo SITE_NAME; ?>" class="logo-img">
                </div>
                <div class="logo-text d-none d-md-block">
                    <strong><?php echo SITE_NAME; ?></strong>
                    <small>Admin Dashboard</small>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="admin-search flex-grow-1 me-auto">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Search dashboard..." id="adminSearch">
            </div>
            
            <!-- Right Side Actions -->
            <div class="ms-auto">
                    <div class="admin-header-actions">
                        <!-- Notifications Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link text-dark position-relative" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end admin-dropdown" aria-labelledby="notificationsDropdown">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><small>New contact submission received</small></a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><small>New event registration</small></a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><small>Impact activity completed</small></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="<?php echo BASE_URL; ?>/admin/index.php">View all notifications</a></li>
                            </ul>
                        </div>
                        
                        <!-- Messages Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link text-dark position-relative" type="button" id="messagesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-envelope fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    2
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end admin-dropdown" aria-labelledby="messagesDropdown">
                                <li><h6 class="dropdown-header">Messages</h6></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><small>New contact message received</small></a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/index.php"><small>Volunteer inquiry</small></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="<?php echo BASE_URL; ?>/admin/index.php">View all messages</a></li>
                            </ul>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link text-dark d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="admin-avatar me-2">
                                    <?php echo e($adminInitials); ?>
                                </div>
                                <span class="me-1"><?php echo e($adminName); ?></span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end admin-dropdown" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/security.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/security.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    </header>

