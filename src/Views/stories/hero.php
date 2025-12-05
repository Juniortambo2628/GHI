<?php
$heroStories = site_setting('hero_stories');
if (! is_array($heroStories) || $heroStories === []) {
    $heroStories = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_stories', []);
}

$heroStoriesTitle = $heroStories['title'] ?? 'Our Stories';
$heroStoriesSubtitle = $heroStories['subtitle'] ?? '';
?>
<!-- Page Header Start -->
<div class="container-fluid page-header hero-stories mb-5">
    <div class="container py-5">
        <nav aria-label="breadcrumb animated slideInDown mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($heroStoriesTitle); ?></li>
            </ol>
        </nav>
        <h1 class="display-3 text-white mb-3 animated slideInDown"><?php echo e($heroStoriesTitle); ?></h1>
        <?php if (!empty($heroStoriesSubtitle)) : ?>
        <p class="text-white-50 hero-page-subtitle animated slideInDown"><?php echo e($heroStoriesSubtitle); ?></p>
        <?php endif;
        ?>
    </div>
</div>
<!-- Page Header End -->


