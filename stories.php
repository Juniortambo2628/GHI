<?php
/**
 * Our Stories Page
 * Global Harmony Initiative Website
 * 
 * Refactored: November 15, 2025
 * Architecture: Service Layer + View Components
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

use GHI\Services\StoriesPageService;

// Set page metadata
$pageTitle = 'Our Stories - ' . SITE_NAME;
$pageDescription = 'Read inspiring stories of impact, transformation, and hope from our work in East Africa.';

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
$storiesService = new StoriesPageService();
$pageData = $storiesService->getPageData([
    'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
    'search' => isset($_GET['search']) ? $_GET['search'] : '',
    'filter' => $filters,
]);

// Include header
require_once __DIR__ . '/includes/header.php';

// Render view components
require __DIR__ . '/src/Views/stories/hero.php';
require __DIR__ . '/src/Views/stories/content.php';
require __DIR__ . '/src/Views/stories/modal.php';

// Include footer
require_once __DIR__ . '/includes/footer.php';
