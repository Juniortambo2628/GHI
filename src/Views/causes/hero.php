<?php
$heroCauses = site_setting('hero_causes');
if (! is_array($heroCauses) || $heroCauses === []) {
    $heroCauses = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_causes', []);
}

$heroCausesTitle = $heroCauses['title'] ?? 'Our Causes';
$heroCausesSubtitle = $heroCauses['subtitle'] ?? '';
?>
<!-- Page Header Start -->
<div class="container-fluid page-header hero-causes mb-5">
    <div class="container py-5">
        <nav aria-label="breadcrumb animated slideInDown mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($heroCausesTitle); ?></li>
            </ol>
        </nav>
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo e($heroCausesTitle); ?></h1>
        <?php if (!empty($heroCausesSubtitle)) : ?>
        <p class="text-white-50 hero-page-subtitle animated slideInDown"><?php echo e($heroCausesSubtitle); ?></p>
        <?php endif;
        ?>
    </div>
</div>
<!-- Page Header End -->


