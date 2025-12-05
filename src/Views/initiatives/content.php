<!-- Initiatives Start -->
<div class="container-fluid px-5">
        <div class="row g-4">
            <?php
            // Sidebar configuration
            $options = [
                'page_title' => 'Our Initiatives',
                'show_search' => true,
                'show_filters' => true,
                'filters' => $pageData['filters'],
                'current_page' => $pageData['currentPage'],
                'total_pages' => $pageData['totalPages'],
                'total_items' => $pageData['totalItems'],
                'items_per_page' => $pageData['itemsPerPage'],
                'base_url' => BASE_URL . '/initiatives.php',
                'current_page_name' => 'initiatives',
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
                    $storageKey = 'viewMode_initiatives';
                    require dirname(__DIR__, 3) . '/includes/grid-list-toggle.php';
                    ?>
                </div>
                
                <!-- Content Container -->
                <div class="content-container <?php echo $viewMode; ?>-view mt-4">
                    <div class="row g-0">
                        <?php if (empty($pageData['initiatives'])) : ?>
                            <div class="col-12">
                                <p class="text-center py-5">No initiatives found. Please try different search or filter criteria.</p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($pageData['initiatives'] as $initiative) :
                                $progress = $initiative['events_planned'] > 0 ? ($initiative['events_completed'] / $initiative['events_planned']) * 100 : 0;
                                $categoryToObjective = [
                                    'education' => 'Education Access & Youth Development',
                                    'health' => 'Health & Well-being',
                                    'livelihood' => 'Poverty Alleviation & Livelihoods',
                                    'empowerment' => 'Community Empowerment',
                                    'partnerships' => 'Global Partnerships & Awareness',
                                ];
                                $objectiveName = $categoryToObjective[$initiative['category']] ?? 'Community Development';
                                ?>
                                <div class="col-md-6 col-lg-4 listing-item">
                                    <div class="card">
                                        <?php echo getResponsiveImage($initiative['image'], [
                                            'width' => 400,
                                            'height' => 300,
                                            'alt' => e($initiative['title']),
                                            'class' => 'card-img-top',
                                            'loading' => 'lazy',
                                            'sizes' => '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw',
                                        ]); ?>
                                        <span class="badge"><?php echo e($objectiveName); ?></span>
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo e($initiative['title']); ?></h5>
                                            <p class="card-text"><?php echo e(truncate($initiative['description'], 100)); ?></p>
                                            <div class="mb-3">
                                                <small class="text-white">Progress: <?php echo $initiative['events_completed']; ?>/<?php echo $initiative['events_planned']; ?> Events</small>
                                                <progress
                                                    class="initiative-progress-track progress-color-secondary"
                                                    value="<?php echo $progress; ?>"
                                                    max="100"
                                                    aria-valuenow="<?php echo $progress; ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                ></progress>
                                            </div>
                                            <button class="btn btn-primary" data-open-initiative-modal="<?php echo htmlspecialchars(json_encode($initiative), ENT_QUOTES, 'UTF-8'); ?>">View Details</button>
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
                $baseUrl = BASE_URL . '/initiatives.php';
                require dirname(__DIR__, 3) . '/includes/pagination.php';
                ?>
            </div>
        </div>
    </div>
<!-- Initiatives End -->


