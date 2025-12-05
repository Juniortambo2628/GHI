<!-- Stories Start -->
<div class="container-fluid px-5">
        <div class="row g-4">
            <?php
            // Sidebar configuration
            $options = [
                'page_title' => 'Our Stories',
                'show_search' => true,
                'show_filters' => true,
                'filters' => $pageData['filters'],
                'current_page' => $pageData['currentPage'],
                'total_pages' => $pageData['totalPages'],
                'total_items' => $pageData['totalItems'],
                'items_per_page' => $pageData['itemsPerPage'],
                'base_url' => BASE_URL . '/stories.php',
                'current_page_name' => 'stories',
            ];
            require dirname(__DIR__, 3) . '/includes/sidebar.php';
            ?>

            <div class="col-lg-9">
                <!-- Pagination Info and Grid/List Toggle -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <p class="mb-0">Showing <?php echo min(($pageData['currentPage'] - 1) * $pageData['itemsPerPage'] + 1, $pageData['totalItems']); ?>-<?php echo min($pageData['currentPage'] * $pageData['itemsPerPage'], $pageData['totalItems']); ?> of <?php echo $pageData['totalItems']; ?> results</p>
                    </div>
                    <?php
                    $viewMode = 'grid';
                    $storageKey = 'viewMode_stories';
                    require dirname(__DIR__, 3) . '/includes/grid-list-toggle.php';
                    ?>
                </div>

                <!-- Content Container -->
                <div class="content-container <?php echo $viewMode; ?>-view mt-4">
                    <div class="row g-4">
                        <?php if (empty($pageData['stories'])) : ?>
                            <div class="col-12">
                                <p class="text-center py-5">No stories found. Please try different search or filter criteria.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($pageData['stories'] as $story) : ?>
                                <div class="col-md-6 col-lg-4 listing-item">
                                    <div class="card">
                                        <?php echo getResponsiveImage($story['image'], [
                                            'width' => 400,
                                            'height' => 300,
                                            'alt' => e($story['title']),
                                            'class' => 'card-img-top',
                                            'loading' => 'lazy',
                                            'sizes' => '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw',
                                        ]); ?>
                                        <span class="badge"><?php echo e(ucfirst((string) $story['region'])); ?></span>
                                        <span class="badge badge-right"><?php echo e(ucfirst((string) $story['category'])); ?></span>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo e($story['title']); ?></h5>
                                            <p class="card-text"><?php echo e(truncate($story['description'], 100)); ?></p>
                                            <small class="text-white mb-2"><i class="fas fa-calendar me-1"></i><?php echo formatDate($story['date']); ?></small>
                                            <small class="text-white mb-3"><i class="fas fa-user me-1"></i><?php echo e($story['author']); ?></small>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <button class="btn btn-sm story-social-btn" data-like-story="<?php echo $story['id']; ?>">
                                                    <i class="fas fa-heart"></i> <span id="likes-<?php echo $story['id']; ?>"><?php echo $story['likes']; ?></span>
                                                </button>
                                                <button class="btn btn-sm story-social-btn" data-comment-story="<?php echo $story['id']; ?>">
                                                    <i class="fas fa-comment"></i> <span><?php echo $story['comments']; ?></span>
                                                </button>
                                                <button class="btn btn-sm story-social-btn" data-share-story="<?php echo $story['id']; ?>">
                                                    <i class="fas fa-share"></i> <span><?php echo $story['shares']; ?></span>
                                                </button>
                                            </div>
                                            <button class="btn btn-primary" data-open-story-modal="<?php echo htmlspecialchars(json_encode($story), ENT_QUOTES, 'UTF-8'); ?>">Read More</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                            ?>
                        <?php endif;
                        ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php
                $currentPage = $pageData['currentPage'];
                $totalPages = $pageData['totalPages'];
                $baseUrl = BASE_URL . '/stories.php';
                require dirname(__DIR__, 3) . '/includes/pagination.php';
                ?>
            </div>
        </div>
    </div>
<!-- Stories End -->
