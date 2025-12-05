<?php
$heroEvents = site_setting('hero_events');
if (! is_array($heroEvents) || $heroEvents === []) {
    $heroEvents = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_events', []);
}

$heroEventsTitle = $heroEvents['title'] ?? 'Events & Activities';
$heroEventsSubtitle = $heroEvents['subtitle'] ?? '';
?>
<!-- Page Header Start -->
<div class="container-fluid page-header hero-events mb-5">
    <div class="container py-5">
        <nav aria-label="breadcrumb animated slideInDown mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($heroEventsTitle); ?></li>
            </ol>
        </nav>
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo e($heroEventsTitle); ?></h1>
        <?php if (!empty($heroEventsSubtitle)) : ?>
        <p class="text-white-50 hero-page-subtitle animated slideInDown"><?php echo e($heroEventsSubtitle); ?></p>
        <?php endif;
        ?>
    </div>
</div>
<!-- Page Header End -->


