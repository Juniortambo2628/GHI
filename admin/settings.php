<?php
/**
 * Settings Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\SiteSettingsService;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$settingsService = SiteSettingsService::getInstance();

$formErrors = [
    'general' => '',
    'mission' => [],
    'quote' => [],
    'hero_home' => [],
    'hero_pages' => [],
];
$successMessages = [];
$heroPageKeys = [
    'hero_causes' => 'Our Causes',
    'hero_initiatives' => 'Our Initiatives',
    'hero_events' => 'Events & Activities',
    'hero_impact' => 'Our Impact',
    'hero_stories' => 'Our Stories',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST[CSRF_TOKEN_NAME] ?? $_POST['_token'] ?? '';
    if (!csrf_validate($token, 'settings')) {
        $formErrors['general'] = 'Invalid security token. Please refresh and try again.';
    } else {
        $action = $_POST['settings_action'] ?? '';
        switch ($action) {
            case 'mission_vision':
                $mission = trim($_POST['mission_statement'] ?? '');
                $vision = trim($_POST['vision_statement'] ?? '');

                if ($mission === '') {
                    $formErrors['mission']['mission_statement'] = 'Mission statement is required.';
                }
                
                if ($vision === '') {
                    $formErrors['mission']['vision_statement'] = 'Vision statement is required.';
                }

                if (empty($formErrors['mission'])) {
                    $settingsService->set('mission_statement', $mission);
                    $settingsService->set('vision_statement', $vision);
                    $successMessages[] = 'Mission & Vision statements updated successfully.';
                }
                
                break;

            case 'quote_banner':
                $quoteText = trim($_POST['quote_banner_text'] ?? '');
                $quoteCitation = trim($_POST['quote_banner_citation'] ?? '');
                $quoteBackground = trim($_POST['quote_banner_background'] ?? '');

                if ($quoteText === '') {
                    $formErrors['quote']['quote_banner_text'] = 'Quote text is required.';
                }
                
                if ($quoteCitation === '') {
                    $formErrors['quote']['quote_banner_citation'] = 'Citation is required.';
                }

                if (empty($formErrors['quote'])) {
                    $settingsService->set('quote_banner_text', $quoteText);
                    $settingsService->set('quote_banner_citation', $quoteCitation);
                    $settingsService->set('quote_banner_background', $quoteBackground, 'quote');
                    $successMessages[] = 'Quote banner updated successfully.';
                }
                
                break;

            case 'hero_home':
                $slidesInput = $_POST['hero_home_slides'] ?? [];
                $normalizedSlides = [];
                foreach ($slidesInput as $slide) {
                    $normalizedSlides[] = [
                        'image' => trim($slide['image'] ?? ''),
                        'heading' => trim($slide['heading'] ?? ''),
                        'subheading' => trim($slide['subheading'] ?? ''),
                        'primary_text' => trim($slide['primary_text'] ?? ''),
                        'primary_url' => trim($slide['primary_url'] ?? ''),
                        'secondary_text' => trim($slide['secondary_text'] ?? ''),
                        'secondary_url' => trim($slide['secondary_url'] ?? ''),
                    ];
                }

                $normalizedSlides = array_values(array_filter($normalizedSlides, fn($slide): bool => isset($slide['heading']) && ($slide['heading'] !== '' && $slide['heading'] !== '0') || isset($slide['image']) && ($slide['image'] !== '' && $slide['image'] !== '0')));

                if ($normalizedSlides === []) {
                    $formErrors['hero_home']['general'] = 'Please provide at least one slide.';
                } else {
                    $settingsService->set('hero_home_slides', $normalizedSlides, 'hero');
                    $successMessages[] = 'Homepage hero slides updated.';
                }
                
                break;

            case 'hero_pages':
                $pagesInput = $_POST['hero_pages'] ?? [];
                foreach ($heroPageKeys as $key => $label) {
                    $pageData = $pagesInput[$key] ?? [];
                    $title = trim($pageData['title'] ?? '');
                    $subtitle = trim($pageData['subtitle'] ?? '');
                    $background = trim($pageData['background'] ?? '');

                    if ($title === '') {
                        $formErrors['hero_pages'][$key]['title'] = 'Title is required.';
                    }

                    if (!isset($formErrors['hero_pages'][$key])) {
                        $settingsService->set($key, [
                            'title' => $title !== '' && $title !== '0' ? $title : $label,
                            'subtitle' => $subtitle,
                            'background' => $background,
                        ], 'hero');
                    }
                }

                if (empty($formErrors['hero_pages'])) {
                    $successMessages[] = 'Hero sections updated successfully.';
                }
                
                break;

            default:
                $formErrors['general'] = 'Unknown action requested.';
                break;
        }
    }
}

$missionStatement = site_setting('mission_statement', $settingsService->getDefault('mission_statement'));
$visionStatement = site_setting('vision_statement', $settingsService->getDefault('vision_statement'));
$quoteBannerText = site_setting('quote_banner_text', $settingsService->getDefault('quote_banner_text'));
$quoteBannerCitation = site_setting('quote_banner_citation', $settingsService->getDefault('quote_banner_citation'));
$quoteBannerBackground = site_setting('quote_banner_background', $settingsService->getDefault('quote_banner_background'));

$defaultSlides = $settingsService->getDefault('hero_home_slides', []);
$heroHomeSlides = site_setting('hero_home_slides', $defaultSlides);
if (!is_array($heroHomeSlides) || $heroHomeSlides === []) {
    $heroHomeSlides = $defaultSlides;
}

$slideCount = max(count($heroHomeSlides), count($defaultSlides), 3);
for ($i = 0; $i < $slideCount; $i++) {
    if (!isset($heroHomeSlides[$i])) {
        $heroHomeSlides[$i] = $defaultSlides[$i] ?? [
            'image' => '',
            'heading' => '',
            'subheading' => '',
            'primary_text' => '',
            'primary_url' => '',
            'secondary_text' => '',
            'secondary_url' => '',
        ];
    }
}

$heroPages = [];
foreach ($heroPageKeys as $key => $label) {
    $value = site_setting($key, $settingsService->getDefault($key, []));
    if (!is_array($value)) {
        $value = $settingsService->getDefault($key, []);
    }
    
    $heroPages[$key] = array_merge([
        'title' => $label,
        'subtitle' => '',
        'background' => '',
    ], $value ?? []);
}

// Set page variables
$pageTitle = 'Settings';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Settings', 'url' => BASE_URL . '/admin/settings.php'],
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
                <div class="col-lg-3 mb-4">
                    <div class="card sticky-card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Navigation</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#missionVision" class="list-group-item list-group-item-action">Mission & Vision</a>
                            <a href="#quoteBanner" class="list-group-item list-group-item-action">Quote Banner</a>
                            <a href="#heroHome" class="list-group-item list-group-item-action">Homepage Hero</a>
                            <a href="#heroPages" class="list-group-item list-group-item-action">Page Heroes</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <?php if ($formErrors['general'] !== '' && $formErrors['general'] !== '0'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo e($formErrors['general']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif;
 ?>

                    <?php foreach ($successMessages as $message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo e($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach;
 ?>

                    <!-- Mission & Vision -->
                    <section id="missionVision" class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Mission & Vision</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <?php echo csrf_field('settings'); ?>
                                <input type="hidden" name="settings_action" value="mission_vision">

                                <div class="mb-4">
                                    <label for="mission_statement" class="form-label fw-semibold">Mission Statement</label>
                                    <textarea
                                        class="form-control <?php echo empty($formErrors['mission']['mission_statement']) ? '' : 'is-invalid'; ?>"
                                        id="mission_statement"
                                        name="mission_statement"
                                        rows="4"
                                        required
                                    ><?php echo e($missionStatement); ?></textarea>
                                    <?php if (isset($formErrors['mission']['mission_statement']) && ($formErrors['mission']['mission_statement'] !== '' && $formErrors['mission']['mission_statement'] !== '0')): ?>
                                        <div class="invalid-feedback"><?php echo e($formErrors['mission']['mission_statement']); ?></div>
                                    <?php endif;
 ?>
                                </div>

                                <div class="mb-4">
                                    <label for="vision_statement" class="form-label fw-semibold">Vision Statement</label>
                                    <textarea
                                        class="form-control <?php echo empty($formErrors['mission']['vision_statement']) ? '' : 'is-invalid'; ?>"
                                        id="vision_statement"
                                        name="vision_statement"
                                        rows="4"
                                        required
                                    ><?php echo e($visionStatement); ?></textarea>
                                    <?php if (isset($formErrors['mission']['vision_statement']) && ($formErrors['mission']['vision_statement'] !== '' && $formErrors['mission']['vision_statement'] !== '0')): ?>
                                        <div class="invalid-feedback"><?php echo e($formErrors['mission']['vision_statement']); ?></div>
                                    <?php endif;
 ?>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Save Mission & Vision
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Quote Banner -->
                    <section id="quoteBanner" class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Quote Banner</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <?php echo csrf_field('settings'); ?>
                                <input type="hidden" name="settings_action" value="quote_banner">

                                <div class="mb-4">
                                    <label for="quote_banner_text" class="form-label fw-semibold">Quote Text</label>
                                    <textarea
                                        class="form-control <?php echo empty($formErrors['quote']['quote_banner_text']) ? '' : 'is-invalid'; ?>"
                                        id="quote_banner_text"
                                        name="quote_banner_text"
                                        rows="3"
                                        required
                                    ><?php echo e($quoteBannerText); ?></textarea>
                                    <?php if (isset($formErrors['quote']['quote_banner_text']) && ($formErrors['quote']['quote_banner_text'] !== '' && $formErrors['quote']['quote_banner_text'] !== '0')): ?>
                                        <div class="invalid-feedback"><?php echo e($formErrors['quote']['quote_banner_text']); ?></div>
                                    <?php endif;
 ?>
                                </div>

                                <div class="mb-4">
                                    <label for="quote_banner_citation" class="form-label fw-semibold">Citation</label>
                                    <input
                                        type="text"
                                        class="form-control <?php echo empty($formErrors['quote']['quote_banner_citation']) ? '' : 'is-invalid'; ?>"
                                        id="quote_banner_citation"
                                        name="quote_banner_citation"
                                        value="<?php echo e($quoteBannerCitation); ?>"
                                        required
                                    >
                                    <?php if (isset($formErrors['quote']['quote_banner_citation']) && ($formErrors['quote']['quote_banner_citation'] !== '' && $formErrors['quote']['quote_banner_citation'] !== '0')): ?>
                                        <div class="invalid-feedback"><?php echo e($formErrors['quote']['quote_banner_citation']); ?></div>
                                    <?php endif;
 ?>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Background Image</label>
                                    <?php if (!empty($quoteBannerBackground)): ?>
                                        <div class="current-image-container mb-3">
                                            <div class="current-image-label">
                                                <i class="bi bi-image-fill me-2"></i>
                                                <span>Current Background</span>
                                            </div>
                                            <div class="current-image-wrapper">
                                                <img src="<?php echo getImageUrl($quoteBannerBackground); ?>" alt="Quote background" class="current-image-preview">
                                            </div>
                                        </div>
                                    <?php endif;
 ?>
                                    <div class="upload-area">
                                        <input
                                            type="file"
                                            class="form-control filepond-input"
                                            id="quoteBannerBackgroundUpload"
                                            name="quoteBannerBackgroundUpload"
                                            accept="image/*"
                                        >
                                    </div>
                                    <input type="hidden" name="quote_banner_background" value="<?php echo e($quoteBannerBackground); ?>">
                                    <small class="text-muted d-block mt-2">Recommended size: 1920x1080. Uploading a new image will replace the current background.</small>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Save Quote Banner
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Homepage Hero -->
                    <section id="heroHome" class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Homepage Hero Slides</h4>
                            <span class="text-muted small">Displayed on the landing page carousel.</span>
                        </div>
                        <div class="card-body">
                            <?php if (isset($formErrors['hero_home']['general']) && ($formErrors['hero_home']['general'] !== '' && $formErrors['hero_home']['general'] !== '0')): ?>
                                <div class="alert alert-danger"><?php echo e($formErrors['hero_home']['general']); ?></div>
                            <?php endif;
 ?>
                            <form method="POST" action="">
                                <?php echo csrf_field('settings'); ?>
                                <input type="hidden" name="settings_action" value="hero_home">

                                <div class="row g-4">
                                    <?php foreach ($heroHomeSlides as $index => $slide): ?>
                                        <div class="col-12">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h5 class="mb-0">Slide <?php echo $index + 1; ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Slide Image</label>
                                                                <?php if (!empty($slide['image'])): ?>
                                                                    <div class="current-image-container mb-3">
                                                                        <div class="current-image-label">
                                                                            <i class="bi bi-image-fill me-2"></i>
                                                                            <span>Current Image</span>
                                                                        </div>
                                                                        <div class="current-image-wrapper">
                                                                            <img src="<?php echo getImageUrl($slide['image']); ?>" alt="Hero slide" class="current-image-preview">
                                                                        </div>
                                                                    </div>
                                                                <?php endif;
 ?>
                                                                <div class="upload-area">
                                                                    <input
                                                                        type="file"
                                                                        class="form-control filepond-input"
                                                                        id="heroSlideUpload_<?php echo $index; ?>"
                                                                        name="heroSlideUpload_<?php echo $index; ?>"
                                                                        accept="image/*"
                                                                    >
                                                                </div>
                                                                <input type="hidden" name="hero_home_slides[<?php echo $index; ?>][image]" value="<?php echo e($slide['image']); ?>">
                                                                <small class="text-muted d-block mt-2">Recommended size: 1920x1080.</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Heading</label>
                                                                <input type="text" class="form-control" name="hero_home_slides[<?php echo $index; ?>][heading]" value="<?php echo e($slide['heading']); ?>" placeholder="Slide headline">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Subheading</label>
                                                                <input type="text" class="form-control" name="hero_home_slides[<?php echo $index; ?>][subheading]" value="<?php echo e($slide['subheading']); ?>" placeholder="Optional supporting text">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Primary Button Text</label>
                                                                        <input type="text" class="form-control" name="hero_home_slides[<?php echo $index; ?>][primary_text]" value="<?php echo e($slide['primary_text']); ?>" placeholder="e.g., Get Involved">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Primary Button URL</label>
                                                                        <input type="text" class="form-control" name="hero_home_slides[<?php echo $index; ?>][primary_url]" value="<?php echo e($slide['primary_url']); ?>" placeholder="/get-involved.php or https://...">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Secondary Button Text</label>
                                                                        <input type="text" class="form-control" name="hero_home_slides[<?php echo $index; ?>][secondary_text]" value="<?php echo e($slide['secondary_text']); ?>" placeholder="Optional button">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Secondary Button URL</label>
                                                                        <input type="text" class="form-control" name="hero_home_slides[<?php echo $index; ?>][secondary_url]" value="<?php echo e($slide['secondary_url']); ?>" placeholder="/donate.php or https://...">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
<?php endforeach;
 ?>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Save Hero Slides
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>

                    <!-- Page Heroes -->
                    <section id="heroPages" class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Page Hero Sections</h4>
                            <span class="text-muted small">Controls the headline and banner for each main page.</span>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <?php echo csrf_field('settings'); ?>
                                <input type="hidden" name="settings_action" value="hero_pages">

                                <div class="row g-4">
                                    <?php foreach ($heroPages as $key => $settings): ?>
                                        <div class="col-12">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h5 class="mb-0"><?php echo e($heroPageKeys[$key]); ?></h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Background Image</label>
                                                                <?php if (!empty($settings['background'])): ?>
                                                                    <div class="current-image-container mb-3">
                                                                        <div class="current-image-label">
                                                                            <i class="bi bi-image-fill me-2"></i>
                                                                            <span>Current Background</span>
                                                                        </div>
                                                                        <div class="current-image-wrapper">
                                                                            <img src="<?php echo getImageUrl($settings['background']); ?>" alt="<?php echo e($heroPageKeys[$key]); ?> hero" class="current-image-preview">
                                                                        </div>
                                                                    </div>
                                                                <?php endif;
 ?>
                                                                <div class="upload-area">
                                                                    <input
                                                                        type="file"
                                                                        class="form-control filepond-input"
                                                                        id="<?php echo $key; ?>Upload"
                                                                        name="<?php echo $key; ?>Upload"
                                                                        accept="image/*"
                                                                    >
                                                                </div>
                                                                <input type="hidden" name="hero_pages[<?php echo $key; ?>][background]" value="<?php echo e($settings['background']); ?>">
                                                                <small class="text-muted d-block mt-2">Recommended size: 1920x600.</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Title</label>
                                                                <input
                                                                    type="text"
                                                                    class="form-control <?php echo empty($formErrors['hero_pages'][$key]['title']) ? '' : 'is-invalid'; ?>"
                                                                    name="hero_pages[<?php echo $key; ?>][title]"
                                                                    value="<?php echo e($settings['title']); ?>"
                                                                    required
                                                                >
                                                                <?php if (isset($formErrors['hero_pages'][$key]['title']) && ($formErrors['hero_pages'][$key]['title'] !== '' && $formErrors['hero_pages'][$key]['title'] !== '0')): ?>
                                                                    <div class="invalid-feedback"><?php echo e($formErrors['hero_pages'][$key]['title']); ?></div>
                                                                <?php endif;
 ?>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Subtitle (optional)</label>
                                                                <input type="text" class="form-control" name="hero_pages[<?php echo $key; ?>][subtitle]" value="<?php echo e($settings['subtitle']); ?>" placeholder="Short supporting line">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
<?php endforeach;
 ?>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Save Page Heroes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php
$formHandlerAsset = get_vite_asset('form-handler*.js', 'dist/js') ?? (BASE_URL . '/admin/js/form-handler.js');
?>
<script type="module" src="<?php echo $formHandlerAsset; ?>"></script>
