<?php
$heroSlides = site_setting('hero_home_slides');
if (! is_array($heroSlides) || $heroSlides === []) {
    $heroSlides = \GHI\Services\SiteSettingsService::getInstance()->getDefault('hero_home_slides', []);
}

$heroSlides = array_values($heroSlides);
?>
<!-- Carousel Start -->
<div class="container-fluid carousel-header vh-100 px-0">
    <div id="carouselId" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <ol class="carousel-indicators">
            <?php foreach (array_keys($heroSlides) as $index) : ?>
            <li data-bs-target="#carouselId" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
            <?php endforeach;
            ?>
        </ol>
        <div class="carousel-inner" role="listbox">
            <?php foreach ($heroSlides as $index => $slide) :
                $imageUrl = getImageUrl($slide['image'] ?? null);
                $loading = $index === 0 ? 'eager' : 'lazy';
                $fetchPriority = $index === 0 ? 'fetchpriority="high"' : '';
                $heading = $slide['heading'] ?? SITE_NAME;
                $subheading = $slide['subheading'] ?? '';
                $primaryText = $slide['primary_text'] ?? 'Get Involved';
                $primaryUrl = resolve_url($slide['primary_url'] ?? '/coming-soon-get-involved.php');
                $secondaryText = $slide['secondary_text'] ?? 'Donate Now';
                $secondaryUrl = resolve_url($slide['secondary_url'] ?? '/coming-soon-donate.php');
                ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="<?php echo e($imageUrl); ?>" class="img-fluid hero-carousel-img" alt="<?php echo e($heading); ?>" loading="<?php echo $loading; ?>" <?php echo $fetchPriority; ?> width="1920" height="1080">
                <div class="carousel-caption">
                    <div class="p-3 hero-caption-container">
                        <?php if (!empty($subheading)) : ?>
                        <p class="text-uppercase text-white-50 mb-3 hero-subheading"><?php echo e($subheading); ?></p>
                        <?php endif;
                        ?>
                        <h1 class="display-1 text-capitalize text-white mb-4"><?php echo e($heading); ?></h1>
                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                            <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="<?php echo e($primaryUrl); ?>"><?php echo e($primaryText); ?></a>
                            <?php if (!empty($secondaryText)) : ?>
                            <a class="btn-hover-bg btn btn-secondary text-dark py-3 px-5" href="<?php echo e($secondaryUrl); ?>"><?php echo e($secondaryText); ?></a>
                            <?php endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach;
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
<!-- Carousel End -->

