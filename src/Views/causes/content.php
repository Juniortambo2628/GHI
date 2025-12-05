<!-- Causes Start -->
<div class="container-fluid px-5">

        <div class="row">
            <?php
            // Sidebar configuration
            $options = [
                'page_title' => 'Our Causes',
                'show_search' => true,
                'show_filters' => true,
                'filters' => $pageData['filters'],
                'current_page' => $pageData['currentPage'],
                'total_pages' => $pageData['totalPages'],
                'total_items' => $pageData['totalItems'],
                'items_per_page' => $pageData['itemsPerPage'],
                'base_url' => BASE_URL . '/causes.php',
                'current_page_name' => 'causes',
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
                    $storageKey = 'viewMode_causes';
                    require dirname(__DIR__, 3) . '/includes/grid-list-toggle.php';
                    ?>
                </div>
                
                <!-- Content Container -->
                <div class="content-container <?php echo $viewMode; ?>-view mt-4">
                    <div class="row g-0">
                        <?php if (empty($pageData['causes'])) : ?>
                            <div class="col-12">
                                <p class="text-center py-5">No causes found. Please try different search or filter criteria.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($pageData['causes'] as $cause) : ?>
                                <div class="col-md-6 col-lg-4 listing-item">
                                    <div class="card">
                                        <?php echo getResponsiveImage($cause['image'], [
                                            'width' => 400,
                                            'height' => 300,
                                            'alt' => e($cause['title']),
                                            'class' => 'card-img-top',
                                            'loading' => 'lazy',
                                            'sizes' => '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw',
                                        ]); ?>
                                        <?php if (!empty($cause['quote'])) : ?>
                                            <span class="badge bg-primary"><?php echo e(truncateWords($cause['quote'], 5)); ?></span>
                                        <?php endif;
                                        ?>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo e($cause['title']); ?></h5>
                                            <p class="card-text"><?php echo e(truncate($cause['description'], 100)); ?></p>
                                            <button class="btn btn-primary" data-open-cause-modal="<?php echo htmlspecialchars(json_encode($cause), ENT_QUOTES, 'UTF-8'); ?>">View Details</button>
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
                $baseUrl = BASE_URL . '/causes.php';
                require dirname(__DIR__, 3) . '/includes/pagination.php';
                ?>
            </div>
        </div>
    </div>
<!-- Causes End -->


