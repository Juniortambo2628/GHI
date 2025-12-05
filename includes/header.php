<?php
/**
 * Site Header Component
 * Global Harmony Initiative Website
 */

// Load configuration if not already loaded
if (! defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}

// Set default page title and description if not set
$pageTitle ??= SITE_NAME . ' - ' . SITE_TAGLINE;
$pageDescription ??= 'Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development.';

$heroSlides = site_setting('hero_home_slides');
if (! is_array($heroSlides) || $heroSlides === []) {
    $heroSlides = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_home_slides', []);
}

$preloadImages = [];
foreach ($heroSlides as $slide) {
    $imageUrl = getImageUrl($slide['image'] ?? null);
    if (! in_array($imageUrl, $preloadImages, true)) {
        $preloadImages[] = $imageUrl;
    }
}

$preloadImages = array_slice($preloadImages, 0, 3);
$themeCssFile = BASE_PATH . '/css/site-theme.css';
?>
<?php
require __DIR__ . '/../src/Views/partials/head.php';
require __DIR__ . '/../src/Views/partials/spinner.php';
require __DIR__ . '/../src/Views/partials/navbar.php';
?>

