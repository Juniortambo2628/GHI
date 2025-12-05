<?php

/**
 * Admin Sidebar Component
 * Global Harmony Initiative Admin Dashboard
 */

// Get current page for active state
$currentPage = basename((string) $_SERVER['PHP_SELF'], '.php');
$currentPage = $currentPage === 'index' ? 'dashboard' : $currentPage;

// Define menu items
$menuItems = [
    'dashboard' => [
        'icon' => 'bi-speedometer2',
        'label' => 'Dashboard',
        'url' => BASE_URL . '/admin/index.php',
    ],
    'causes' => [
        'icon' => 'bi-heart',
        'label' => 'Causes',
        'url' => BASE_URL . '/admin/causes.php',
    ],
    'initiatives' => [
        'icon' => 'bi-lightbulb',
        'label' => 'Initiatives',
        'url' => BASE_URL . '/admin/initiatives.php',
    ],
    'events' => [
        'icon' => 'bi-calendar-event',
        'label' => 'Events',
        'url' => BASE_URL . '/admin/events.php',
    ],
    'stories' => [
        'icon' => 'bi-journal-text',
        'label' => 'Stories',
        'url' => BASE_URL . '/admin/stories.php',
    ],
    'contact' => [
        'icon' => 'bi-envelope',
        'label' => 'Contact Submissions',
        'url' => BASE_URL . '/admin/contact-submissions.php',
    ],
    'newsletter' => [
        'icon' => 'bi-mailbox',
        'label' => 'Newsletter',
        'url' => BASE_URL . '/admin/newsletter.php',
    ],
    'donations' => [
        'icon' => 'bi-cash-stack',
        'label' => 'Donations',
        'url' => BASE_URL . '/admin/donations.php',
    ],
];

$settingsItems = [
    'settings' => [
        'icon' => 'bi-gear',
        'label' => 'Settings',
        'url' => BASE_URL . '/admin/settings.php',
    ],
    'security' => [
        'icon' => 'bi-shield-check',
        'label' => 'Security',
        'url' => BASE_URL . '/admin/security.php',
    ],
    'sessions' => [
        'icon' => 'bi-clock-history',
        'label' => 'Sessions',
        'url' => BASE_URL . '/admin/sessions.php',
    ],
];
?>
<aside class="admin-sidebar">
    <div class="sidebar-content">
        <!-- Logo Section -->
        <div class="sidebar-logo">
            <div class="logo-image">
                <img src="<?php echo LOGO_URL; ?>/Square-White-BG.png" alt="<?php echo SITE_NAME; ?>" class="logo-img">
            </div>
            <div class="logo-text">
                <strong><?php echo strtoupper(SITE_NAME); ?></strong><br>
                <small><?php echo strtoupper(SITE_TAGLINE); ?></small>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <?php foreach ($menuItems as $key => $item) : ?>
                    <li class="nav-item <?php echo $currentPage === $key ? 'active' : ''; ?>">
                        <a href="<?php echo e($item['url']); ?>" class="nav-link">
                            <i class="<?php echo e($item['icon']); ?>"></i>
                            <span><?php echo e($item['label']); ?></span>
                        </a>
                    </li>
                <?php endforeach;
                ?>
            </ul>

            <!-- Settings Section -->
            <ul class="nav-menu settings-menu">
                <li class="nav-section-title">Settings</li>
                <?php foreach ($settingsItems as $key => $item) : ?>
                    <li class="nav-item <?php echo $currentPage === $key ? 'active' : ''; ?>">
                        <a href="<?php echo e($item['url']); ?>" class="nav-link">
                            <i class="<?php echo e($item['icon']); ?>"></i>
                            <span><?php echo e($item['label']); ?></span>
                        </a>
                    </li>
                <?php endforeach;
                ?>
            </ul>

            <!-- Actions Section -->
            <ul class="nav-menu actions-menu">
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>" class="nav-link">
                        <i class="bi-house"></i>
                        <span>Back to Site</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="nav-link">
                        <i class="bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

