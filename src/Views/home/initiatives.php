<!-- Our Initiatives Start -->
<div class="container-fluid py-5 causes">
    <div class="container">
        <div class="text-center mx-auto pb-5 section-header-container">
            <h5 class="text-uppercase text-primary">Our Initiatives</h5>
            <h1 class="mb-4">Strategic Initiatives for Lasting Impact</h1>
            <p class="mb-0">We work across multiple areas to address critical needs and create lasting positive change in East African communities.</p>
        </div>
        <div class="row g-0 initiatives-grid">
            <?php
            foreach (array_slice($pageData['initiatives'], 0, 3) as $initiative) :
                $objectiveName = $pageData['categoryToObjective'][$initiative['category']] ?? 'Community Development';

                // Event count is now pre-fetched in service layer (no N+1 query!)
                $eventCount = $initiative['event_count'] ?? 0;

                $progressPercent = $eventCount > 0 ? min(100, round(($eventCount / max($eventCount + 5, 10)) * 100)) : 0;
                $initiativeSlug = $initiative['slug'];
                $initiativeImage = $initiative['image'] ? getImageUrl($initiative['image']) : BASE_URL . '/Banners-and-portraits/pexels-speakmediauganda-33749790.jpg';
                ?>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="causes-item h-100 d-flex flex-column">
                    <div class="causes-img">
                        <img src="<?php echo $initiativeImage; ?>" class="img-fluid w-100 impact-card-img" alt="<?php echo e($initiative['title']); ?>" loading="lazy" width="400" height="300" decoding="async">
                        <div class="causes-link pb-2 px-3">
                            <small class="text-white"><i class="fas fa-calendar-check text-primary me-2"></i>Events: <?php echo $eventCount; ?></small>
                        </div>
                        <div class="causes-dination p-2">
                            <a class="btn-hover-bg btn btn-primary text-white py-2 px-3" href="<?php echo BASE_URL; ?>/initiatives.php?initiative=<?php echo urlencode((string) $initiativeSlug); ?>">View Details</a>
                        </div>
                    </div>
                    <div class="causes-content p-4 flex-grow-1 d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary mb-2"><?php echo e($objectiveName); ?></span>
                        </div>
                        <a class="h5 mb-3 text-dark" href="<?php echo BASE_URL; ?>/initiatives.php?initiative=<?php echo urlencode((string) $initiativeSlug); ?>"><?php echo e($initiative['title']); ?></a>
                        <p class="mb-3"><?php echo e(truncate($initiative['description'], 100)); ?></p>
                        <div class="mt-auto">
                            <progress
                                class="initiative-progress-track progress-color-primary mb-2"
                                value="<?php echo $progressPercent; ?>"
                                max="100"
                                aria-valuenow="<?php echo $progressPercent; ?>"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></progress>
                            <small class="text-muted"><?php echo $progressPercent; ?>% Complete</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach;
            ?>
        </div>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a class="btn-hover-bg btn btn-primary text-white py-3 px-5" href="<?php echo BASE_URL; ?>/initiatives.php">See All Initiatives</a>
            </div>
        </div>
    </div>
</div>
<!-- Our Initiatives End -->


