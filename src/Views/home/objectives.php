<!-- Core Objectives Start -->
<div class="container-fluid py-5 service bg-light">
    <div class="container">
        <div class="text-center mx-auto pb-5 section-header-container">
            <h5 class="text-uppercase text-primary">What we do</h5>
            <h1 class="mb-0">Our Core Objectives</h1>
        </div>
        <div class="row justify-content-center core-objectives-grid">
            <?php
            $imgIndex = 1;
            $objectiveImages = [
                BASE_URL . '/Banners-and-portraits/pexels-lagosfoodbank-6472487.jpg',
                BASE_URL . '/Banners-and-portraits/pexels-lagosfoodbank-8054617.jpg',
                BASE_URL . '/Banners-and-portraits/pexels-speakmediauganda-33749783.jpg',
                BASE_URL . '/Banners-and-portraits/pexels-speakmediauganda-34222337.jpg',
                BASE_URL . '/Banners-and-portraits/pexels-speakmediauganda-33749791.jpg',
            ];

            foreach ($pageData['objectives'] as $objective) :
                $imgIndex = ($imgIndex > 5) ? 1 : $imgIndex;
                ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 core-objective-col">
                <div class="service-item">
                    <img src="<?php echo $objectiveImages[$imgIndex - 1]; ?>" class="img-fluid w-100" alt="<?php echo e($objective['title']); ?>" loading="lazy" width="400" height="300">
                    <div class="service-link">
                        <a href="<?php echo BASE_URL; ?>/initiatives.php?objective=<?php echo urlencode(strtolower(str_replace([' ', '&'], ['-', 'and'], $objective['title']))); ?>" class="h4 mb-0"><?php echo e($objective['title']); ?></a>
                    </div>
                </div>
                <p class="my-4"><?php echo e($objective['description']); ?></p>
                <?php if (! empty($objective['quote'])) : ?>
                <blockquote class="blockquote mb-0">
                    <p class="mb-0 small fst-italic text-primary"><?php echo e($objective['quote']); ?></p>
                </blockquote>
                <?php endif;
                ?>
            </div>
                <?php
                $imgIndex++;
            endforeach;
            ?>
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-center">
                    <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="<?php echo BASE_URL; ?>/initiatives.php">View All Initiatives</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Services End -->


