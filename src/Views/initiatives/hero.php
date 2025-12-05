<?php
$heroInitiatives = site_setting('hero_initiatives');
if (! is_array($heroInitiatives) || $heroInitiatives === []) {
    $heroInitiatives = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_initiatives', []);
}

$heroInitiativesTitle = $heroInitiatives['title'] ?? 'Our Initiatives';
$heroInitiativesSubtitle = $heroInitiatives['subtitle'] ?? '';
?>
<!-- Page Header Start -->
<div class="container-fluid page-header hero-initiatives mb-5">
    <div class="container py-5">
        <nav aria-label="breadcrumb animated slideInDown mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($heroInitiativesTitle); ?></li>
            </ol>
        </nav>
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo e($heroInitiativesTitle); ?></h1>
        <?php if (!empty($heroInitiativesSubtitle)) : ?>
        <p class="text-white-50 hero-page-subtitle animated slideInDown"><?php echo e($heroInitiativesSubtitle); ?></p>
        <?php endif;
        ?>
    </div>
</div>
<!-- Page Header End -->


