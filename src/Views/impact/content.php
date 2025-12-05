<!-- Impact Start -->
<div class="container-fluid px-5">
        <div class="row g-4">
            <?php
            // Sidebar configuration
            $options = [
                'page_title' => 'Our Impact',
                'show_search' => true,
                'show_filters' => true,
                'filters' => $pageData['filters'],
                'current_page' => $pageData['currentPage'],
                'total_pages' => $pageData['totalPages'],
                'total_items' => $pageData['totalItems'],
                'items_per_page' => $pageData['itemsPerPage'],
                'base_url' => BASE_URL . '/impact.php',
                'current_page_name' => 'impact',
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
                    $storageKey = 'viewMode_impact';
                    require dirname(__DIR__, 3) . '/includes/grid-list-toggle.php';
                    ?>
                </div>

                <!-- Content Container -->
                <div class="content-container <?php echo $viewMode; ?>-view mt-4 no-animation" data-disable-animation="true">
                    <div class="row g-0 no-animation" data-disable-animation="true">
                        <?php if (empty($pageData['impacts'])) : ?>
                            <div class="col-12">
                                <p class="text-center py-5">No impact stories found. Please try different search or filter criteria.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($pageData['impacts'] as $impact) :
                                $objectiveLabels = [
                                    'poverty' => 'Poverty Alleviation & Livelihoods',
                                    'education' => 'Education Access & Youth Development',
                                    'health' => 'Health & Well-being',
                                    'empowerment' => 'Community Empowerment',
                                    'partnerships' => 'Global Partnerships & Awareness',
                                ];
                                $objectiveLabel = $objectiveLabels[$impact['objective']] ?? 'Community Development';
                                ?>
                                <div class="col-md-6 col-lg-4 listing-item">
                                    <div class="card">
                                        <?php echo getResponsiveImage($impact['image'] ?: $impact['thumbnail'], [
                                            'width' => 400,
                                            'height' => 300,
                                            'alt' => e($impact['title']),
                                            'class' => 'card-img-top',
                                            'loading' => 'lazy',
                                            'sizes' => '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw',
                                        ]); ?>
                                        <span class="badge"><?php echo e(ucfirst((string) $impact['region'])); ?></span>
                                        <span class="badge badge-right"><?php echo e($objectiveLabel); ?></span>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo e($impact['title']); ?></h5>
                                            <p class="card-text"><?php echo e(truncate($impact['description'], 100)); ?></p>
                                            <small class="text-white mb-2"><i class="fas fa-users me-1"></i><?php echo number_format($impact['lives_impacted'] ?? 0); ?> Lives Impacted</small>
                                            <small class="text-white mb-3"><i class="fas fa-calendar me-1"></i><?php echo formatDate($impact['date']); ?></small>
                                            <button class="btn btn-primary" data-open-impact-modal="<?php echo htmlspecialchars(json_encode($impact), ENT_QUOTES, 'UTF-8'); ?>">View Details</button>
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
                $baseUrl = BASE_URL . '/impact.php';
                require dirname(__DIR__, 3) . '/includes/pagination.php';
                ?>
            </div>
        </div>
    </div>
<!-- Impact End -->


