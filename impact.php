<?php
/**
 * Our Impact Page
 * Global Harmony Initiative Website
 * 
 * Refactored: November 15, 2025
 * Architecture: Service Layer + View Components
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

use GHI\Services\ImpactPageService;

// Set page metadata
$pageTitle = 'Our Impact - ' . SITE_NAME;
$pageDescription = 'Discover the positive impact we are making in East Africa through our programs and initiatives.';

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
$impactService = new ImpactPageService();
$pageData = $impactService->getPageData([
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'search' => isset($_GET['search']) ? $_GET['search'] : '',
    'filter' => $filters,
]);

// Include header
require_once __DIR__ . '/includes/header.php';

// Render view components
require __DIR__ . '/src/Views/impact/hero.php';
require __DIR__ . '/src/Views/impact/content.php';
require __DIR__ . '/src/Views/impact/modal.php';

// Include footer
require_once __DIR__ . '/includes/footer.php';
