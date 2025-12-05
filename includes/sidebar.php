<?php
/**
 * Reusable Sidebar Component
 * Includes: On-page navigation, breadcrumb, search, filters, pagination
 *
 * @param array $options - Configuration options:
 *   - 'page_title' (string): Page title for breadcrumb
 *   - 'show_search' (bool): Show search box (default: true)
 *   - 'show_filters' (bool): Show filter section (default: true)
 *   - 'filters' (array): Filter options array
 *   - 'current_page' (int): Current page number for pagination
 *   - 'total_pages' (int): Total number of pages
 *   - 'total_items' (int): Total number of items
 *   - 'items_per_page' (int): Items per page (default: 12)
 */

// Set defaults
$pageTitle = $options['page_title'] ?? 'Page';
$showSearch = $options['show_search'] ?? true;
$showFilters = $options['show_filters'] ?? true;
$filters = $options['filters'] ?? [];
$currentPage = $options['current_page'] ?? 1;
$totalPages = $options['total_pages'] ?? 1;
$totalItems = $options['total_items'] ?? 0;
$itemsPerPage = $options['items_per_page'] ?? 12;
$baseUrl = $options['base_url'] ?? BASE_URL;
$currentPageName = $options['current_page_name'] ?? getCurrentPage();
?>

<!-- Sidebar Start -->
<div class="col-lg-3">
    <div class="sidebar">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/index.php" class="breadcrumb-link">Home</a></li>
                <li class="breadcrumb-item active breadcrumb-item-custom" aria-current="page"><?php echo e($pageTitle); ?></li>
            </ol>
        </nav>

        <!-- Search -->
        <?php if ($showSearch): ?>
        <div class="sidebar-widget mb-4">
            <h5 class="mb-3">Search</h5>
            <form class="search-form" method="GET" action="">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
        <?php endif;
 ?>

        <!-- Filters -->
        <?php if ($showFilters && ! empty($filters)): ?>
        <div class="sidebar-widget mb-4">
            <h5 class="mb-3">Filters</h5>
            <form class="filter-form" method="GET" action="">
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if ($key !== 'filter' && $key !== 'page'): ?>
                        <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                    <?php endif;
 ?>
<?php endforeach;
 ?>
                
                <?php foreach ($filters as $filterKey => $filter): ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e($filter['label'] ?? ucfirst((string) $filterKey)); ?></label>
                        <?php if ($filter['type'] === 'select'): ?>
                            <select class="form-select" name="filter[<?php echo e($filterKey); ?>]">
                                <option value="">All</option>
                                <?php foreach ($filter['options'] as $optionValue => $optionLabel): ?>
                                    <option value="<?php echo e($optionValue); ?>" <?php echo (isset($_GET['filter'][$filterKey]) && $_GET['filter'][$filterKey] == $optionValue) ? 'selected' : ''; ?>>
                                        <?php echo e($optionLabel); ?>
                                    </option>
                                <?php endforeach;
 ?>
                            </select>
<?php elseif ($filter['type'] === 'checkbox'): ?>
                            <?php foreach ($filter['options'] as $optionValue => $optionLabel): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="filter[<?php echo e($filterKey); ?>][]" 
                                           value="<?php echo e($optionValue); ?>" 
                                           id="filter-<?php echo e($filterKey); ?>-<?php echo e($optionValue); ?>"
                                           <?php echo (isset($_GET['filter'][$filterKey]) && in_array($optionValue, (array)$_GET['filter'][$filterKey])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="filter-<?php echo e($filterKey); ?>-<?php echo e($optionValue); ?>">
                                        <?php echo e($optionLabel); ?>
                                    </label>
                                </div>
                            <?php endforeach;
 ?>
<?php endif;
 ?>
                    </div>
<?php endforeach;
 ?>
                
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                <a href="<?php echo $baseUrl . '/' . $currentPageName . '.php'; ?>" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
            </form>
        </div>
<?php endif;
 ?>

        <!-- Quick Navigation -->
        <div class="sidebar-widget mb-4">
            <h5 class="mb-3">Quick Links</h5>
            <ul class="list-unstyled">
                <li><a href="<?php echo BASE_URL; ?>/causes.php"><i class="fas fa-angle-right me-2"></i> Our Causes</a></li>
                <li><a href="<?php echo BASE_URL; ?>/initiatives.php"><i class="fas fa-angle-right me-2"></i> Initiatives</a></li>
                <li><a href="<?php echo BASE_URL; ?>/events.php"><i class="fas fa-angle-right me-2"></i> Events</a></li>
                <li><a href="<?php echo BASE_URL; ?>/impact.php"><i class="fas fa-angle-right me-2"></i> Our Impact</a></li>
                <li><a href="<?php echo BASE_URL; ?>/stories.php"><i class="fas fa-angle-right me-2"></i> Our Stories</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- Sidebar End -->

