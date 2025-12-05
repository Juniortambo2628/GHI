<?php
$heroImpact = site_setting('hero_impact');
if (! is_array($heroImpact) || $heroImpact === []) {
    $heroImpact = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_impact', []);
}

$heroImpactTitle = $heroImpact['title'] ?? 'Our Impact';
$heroImpactSubtitle = $heroImpact['subtitle'] ?? '';
?>
<!-- Page Header Start -->
<div class="container-fluid page-header hero-impact mb-5">
    <div class="container py-5">
        <nav aria-label="breadcrumb animated slideInDown mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($heroImpactTitle); ?></li>
            </ol>
        </nav>
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo e($heroImpactTitle); ?></h1>
        <?php if (!empty($heroImpactSubtitle)) : ?>
        <p class="text-white-50 hero-page-subtitle animated slideInDown"><?php echo e($heroImpactSubtitle); ?></p>
        <?php endif;
        ?>
    </div>
</div>
<!-- Page Header End -->


