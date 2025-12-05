<?php
/**
 * Home Page
 * Global Harmony Initiative Website
 * 
 * Refactored: November 15, 2025
 * Architecture: Service Layer + View Components
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

use GHI\Services\HomePageService;

// Set page metadata
$pageTitle = SITE_NAME . ' - ' . SITE_TAGLINE;
$pageDescription = 'Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development.';

// Fetch all page data through service layer
$homeService = new HomePageService();
$pageData = $homeService->getPageData();

// Include header
require_once __DIR__ . '/includes/header.php';

// Render view components
require __DIR__ . '/src/Views/home/hero.php';
require __DIR__ . '/src/Views/home/about.php';
require __DIR__ . '/src/Views/home/foundation.php';
require __DIR__ . '/src/Views/home/objectives.php';
require __DIR__ . '/src/Views/home/counter.php';
require __DIR__ . '/src/Views/home/initiatives.php';
require __DIR__ . '/src/Views/home/events.php';
require __DIR__ . '/src/Views/home/stories.php';
require __DIR__ . '/src/Views/home/gallery.php';
require __DIR__ . '/src/Views/home/volunteer.php';

// Include footer
require_once __DIR__ . '/includes/footer.php';
