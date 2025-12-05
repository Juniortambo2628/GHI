<!-- Gallery Start -->
<div class="container-fluid gallery py-5">
    <div class="container py-5">
        <div class="text-center mx-auto pb-5 section-header-container">
        <h5 class="text-uppercase text-primary">Our work</h5>
        <h1 class="mb-4">Recent Activities Gallery</h1>
        <p class="mb-0">See the impact of our programs through images from our most recent activities across East Africa.</p>
    </div>
    <div class="row g-0">
        <?php
        // Gallery activities data - limit to 6 items
        $recentActivities = $pageData['recentActivities'] ?? [
            ['image' => 'Banners-and-portraits/pexels-rdne-6646918.jpg', 'initiative' => 'Women Entrepreneurship Program', 'objective' => 'Community Development', 'location' => 'Mbale, Uganda'],
            ['image' => 'Banners-and-portraits/pexels-caleboquendo-34612590.jpg', 'initiative' => 'School Fee Support Program', 'objective' => 'Community Development', 'location' => 'Dodoma, Tanzania'],
            ['image' => 'Banners-and-portraits/pexels-speakmediauganda-34222337.jpg', 'initiative' => 'Community Health Clinics', 'objective' => 'Community Development', 'location' => 'Zanzibar, Tanzania'],
            ['image' => 'Banners-and-portraits/pexels-finalchoice-147015110-34599374.jpg', 'initiative' => 'Digital Learning Centers', 'objective' => 'Education Access & Youth Development', 'location' => 'Rural Kenya'],
            ['image' => 'Banners-and-portraits/pexels-lom-doudou-351893580-34617622.jpg', 'initiative' => 'Community Health Clinics Partnership', 'objective' => 'Health & Well-being', 'location' => 'Zanzibar'],
            ['image' => 'Banners-and-portraits/pexels-mo-liban-3049584-5648154.jpg', 'initiative' => 'Microfinance & Small Business Support', 'objective' => 'Poverty Alleviation & Livelihoods', 'location' => 'Tanzania'],
        ];
        $recentActivities = array_slice($recentActivities, 0, 6);
        ?>
        <?php foreach ($recentActivities as $index => $activity) : ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="gallery-item">
                <img class="lazy img-fluid w-100 impact-card-img" data-src="<?php echo getImageUrl($activity['image']); ?>" alt="<?php echo e($activity['initiative']); ?>" width="800" height="600" decoding="async">
                <div class="search-icon">
                    <a href="<?php echo getImageUrl($activity['image']); ?>" data-lightbox="gallery-<?php echo $index; ?>" class="my-auto"><i class="fas fa-search-plus btn-hover-color bg-white text-primary p-3"></i></a>
                </div>
                <div class="gallery-content">
                    <div class="gallery-inner pb-5">
                        <a href="<?php echo BASE_URL; ?>/initiatives.php" class="h4 text-white"><?php echo e($activity['initiative']); ?></a>
                        <a href="<?php echo BASE_URL; ?>/initiatives.php" class="text-white"><p class="mb-1"><?php echo e($activity['objective']); ?></p></a>
                        <small class="text-white-50"><i class="fas fa-map-marker-alt me-1"></i><?php echo e($activity['location']); ?></small>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach;
        ?>
    </div>
    </div>
</div>
<!-- Gallery End -->

