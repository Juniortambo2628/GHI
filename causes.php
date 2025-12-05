<?php
/**
 * Our Causes Page
 * Global Harmony Initiative Website
 * 
 * Refactored: November 15, 2025
 * Architecture: Service Layer + View Components
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

use GHI\Services\CausesPageService;

// Set page metadata
$pageTitle = 'Our Causes - ' . SITE_NAME;
$pageDescription = 'Explore our causes and learn how we are making a difference in East Africa through education, healthcare, and community development.';

// Parse URL parameters - handle both flat (?objective=...) and nested (?filter[objective]=...) formats
$filters = $_GET['filter'] ?? [];

// Map objective slug to objective value (e.g., "community-empowerment" -> "empowerment")
if (isset($_GET['objective']) && !isset($filters['objective'])) {
    $objectiveValue = mapObjectiveSlug($_GET['objective']);
    if ($objectiveValue !== null) {
        $filters['objective'] = $objectiveValue;
    }
}

// Fetch page data through service layer
$causesService = new CausesPageService();
$pageData = $causesService->getPageData([
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'search' => isset($_GET['search']) ? $_GET['search'] : '',
    'filter' => $filters,
]);

// Include header
require_once __DIR__ . '/includes/header.php';

// Render view components
require __DIR__ . '/src/Views/causes/hero.php';
require __DIR__ . '/src/Views/causes/content.php';
require __DIR__ . '/src/Views/causes/modal.php';

// Include footer
require_once __DIR__ . '/includes/footer.php';
