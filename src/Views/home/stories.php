<!-- Our Stories Start -->
<div class="container-fluid blog py-5">
    <div class="container">
        <div class="text-center mx-auto pb-5 section-header-container">
            <h5 class="text-uppercase text-primary">Our Impact</h5>
            <h1 class="mb-0">Stories of Transformation and Positive Outcomes</h1>
            <p class="mb-0 mt-3">Discover how our activities are creating lasting change in communities across East Africa. Read, engage, and share these inspiring stories.</p>
        </div>
        <div class="row g-4 no-animation justify-content-center" data-disable-animation="true">
            <?php if (empty($pageData['stories'])) : ?>
                <div class="col-12">
                    <p class="text-center py-5">No stories available at this time. Please check back later.</p>
                </div>
            <?php else : ?>
                <?php foreach (array_slice($pageData['stories'], 0, 3) as $story) : ?>
                    <?php
                        $storyImage = $story['image'] ? getImageUrl($story['image']) : BASE_URL . '/Banners-and-portraits/pexels-ezeguna_graphy-sulaiman-muhammad-2153324075-34536427.jpg';
                        $objectiveName = $pageData['categoryToObjective'][$story['category']] ?? 'Community Development';
                        $storyDate = formatDate($story['created_at']);
                        $storySlug = $story['slug'] ?? 'story-' . $story['id'];
                    ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="blog-item h-100 d-flex flex-column">
                            <div class="blog-img">
                                <img src="<?php echo $storyImage; ?>" class="img-fluid w-100 impact-card-img" alt="<?php echo e($story['title']); ?>" loading="lazy" width="400" height="300">
                                <div class="blog-info">
                                    <span><i class="fa fa-clock"></i> <?php echo $storyDate; ?></span>
                                    <div class="d-flex">
                                        <button class="btn-like me-2 text-white border-0 bg-transparent" data-story-id="<?php echo $story['id']; ?>" title="Like this story">
                                            <span class="like-count"><?php echo $story['likes'] ?? 0; ?></span> <i class="fa fa-heart"></i>
                                        </button>
                                        <a href="<?php echo BASE_URL; ?>/stories.php?story=<?php echo urlencode((string) $storySlug); ?>#comments" class="text-white me-2" title="View comments">
                                            <span><?php echo $story['comments'] ?? 0; ?></span> <i class="fa fa-comment"></i>
                                        </a>
                                        <button class="btn-share text-white border-0 bg-transparent" data-story-id="<?php echo $story['id']; ?>" data-story-title="<?php echo e($story['title']); ?>" title="Share this story">
                                            <i class="fa fa-share-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="blog-content p-4 flex-grow-1 d-flex flex-column">
                                <div class="blog-comment d-flex align-items-center mb-3">
                                    <div class="small">
                                        <span class="badge bg-primary"><?php echo e($objectiveName); ?></span>
                                    </div>
                                </div>
                                <a href="<?php echo BASE_URL; ?>/stories.php?story=<?php echo urlencode((string) $storySlug); ?>" class="h4 d-inline-block mb-3"><?php echo e($story['title']); ?></a>
                                <p class="mb-3 flex-grow-1"><?php echo e(truncate($story['description'], 120)); ?></p>
                                <a href="<?php echo BASE_URL; ?>/stories.php?story=<?php echo urlencode((string) $storySlug); ?>" class="fw-bold text-secondary">Read More <i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
                ?>
            <?php endif;
            ?>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="<?php echo BASE_URL; ?>/stories.php">See All Stories</a>
            </div>
        </div>
    </div>
</div>
<!-- Our Impact End -->


