<?php
/**
 * Initiatives Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/cache-helper.php';

use GHI\Models\Initiative;

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build conditions
$conditions = [];
if ($status) {
    $conditions['status'] = $status;
}

if ($category) {
    $conditions['category'] = $category;
}

// Build order by
if ($sort === 'oldest') {
    $orderBy = 'created_at ASC';
} elseif ($sort === 'title') {
    $orderBy = 'title ASC';
} else {
    $orderBy = 'created_at DESC';
}

// Get initiatives with caching
$initiativeModel = new Initiative();

// Create cache key based on filters
$cacheKey = 'initiatives_' . md5(serialize($conditions) . $orderBy . $perPage . $offset . $search);

// Try to get from cache (5 minute cache)
$cachedData = SimpleCache::get($cacheKey);

if ($cachedData !== null) {
    $initiatives = $cachedData['initiatives'];
    $totalInitiatives = $cachedData['total'];
} else {
    $initiatives = $initiativeModel->all($conditions, $orderBy, $perPage, $offset);
    $totalInitiatives = $initiativeModel->count($conditions);
    
    // Cache the results
    SimpleCache::set($cacheKey, [
        'initiatives' => $initiatives,
        'total' => $totalInitiatives
    ], 300); // 5 minutes
}

$totalPages = ceil($totalInitiatives / $perPage);

// Filter by search if provided
if ($search) {
    $initiatives = array_filter($initiatives, fn($initiative): bool => stripos((string) $initiative['title'], (string) $search) !== false ||
           stripos((string) $initiative['description'], (string) $search) !== false);
}

// Category to objective mapping
$categoryToObjective = [
    'livelihood' => 'Poverty Alleviation & Livelihoods',
    'education' => 'Education Access & Youth Development',
    'health' => 'Health & Well-being',
    'empowerment' => 'Community Empowerment',
    'partnerships' => 'Global Partnerships & Awareness',
];

// Prepare table data
$tableData = array_values(prepare_table_data(
    array_map(fn($initiative): array => [
        'id' => $initiative['id'],
        'image' => $initiative['image'] ? getImageUrl($initiative['image']) : '',
        'title' => $initiative['title'],
        'category' => $initiative['category'],
        'category_label' => ucfirst((string) $initiative['category']),
        'core_objective' => $categoryToObjective[$initiative['category']] ?? 'Community Development',
        'status' => $initiative['status'],
        'status_label' => ucfirst((string) $initiative['status']),
        'created_at' => $initiative['created_at'],
    ], $initiatives),
    BASE_URL . '/admin/initiative-edit.php?id={id}',
    BASE_URL . '/admin/initiative-delete.php?id={id}',
    BASE_URL . '/admin/initiative-view.php?id={id}'
));

// Define table columns
$tableColumns = [
    ['title' => 'Image', 'field' => 'image', 'formatter' => 'image', 'formatterParams' => ['height' => 60, 'width' => 60], 'width' => 100, 'headerSort' => false],
    ['title' => 'Title', 'field' => 'title', 'sorter' => 'string', 'width' => 200],
    ['title' => 'Category', 'field' => 'category_label', 'sorter' => 'string', 'width' => 120],
    ['title' => 'Core Objective', 'field' => 'core_objective', 'sorter' => 'string', 'width' => 200],
    ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
    ['title' => 'Created', 'field' => 'created_at', 'sorter' => 'date', 'width' => 150],
];

// Set page variables
$pageTitle = 'Initiatives Management';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Initiatives', 'url' => BASE_URL . '/admin/initiatives.php'],
];

// Include header
require_once __DIR__ . '/includes/header.php';

// Include sidebar
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <!-- Hero Area -->
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        <div class="container-fluid">
            
            <?php
            // Actions bar configuration
            $pageTitle = 'All Initiatives';
            $addNewUrl = BASE_URL . '/admin/initiative-edit.php';
            $addNewLabel = 'Add New Initiative';
            $exportTableId = 'initiativesTable';
            $showExport = true;
            $showViewSwitcher = true;
            $viewSwitcherKey = 'admin_initiatives_view';
            $defaultView = 'grid';
            require __DIR__ . '/includes/list-page-actions.php';
            ?>
            
            <?php
            // Filters configuration
            $showSearch = true;
            $searchValue = $search;
            $formId = 'initiativesFilterForm';
            $autoSubmit = false;
            $filters = [
                [
                    'type' => 'select',
                    'name' => 'status',
                    'placeholder' => 'All Status',
                    'value' => $status,
                    'options' => ['published' => 'Published', 'draft' => 'Draft']
                ],
                [
                    'type' => 'select',
                    'name' => 'category',
                    'placeholder' => 'All Categories',
                    'value' => $category,
                    'options' => [
                        'education' => 'Education',
                        'health' => 'Health',
                        'livelihood' => 'Livelihood',
                        'empowerment' => 'Empowerment',
                        'partnerships' => 'Partnerships'
                    ]
                ],
                [
                    'type' => 'select',
                    'name' => 'sort',
                    'placeholder' => 'Sort By',
                    'value' => $sort,
                    'options' => [
                        'newest' => 'Newest First',
                        'oldest' => 'Oldest First',
                        'title' => 'Title A-Z'
                    ]
                ],
            ];
            require __DIR__ . '/includes/list-page-filters.php';
            ?>
            
            <?php
            // View container configuration
            $tableId = 'initiativesTable';
            $gridTemplate = __DIR__ . '/includes/grid-templates/initiative-card.php';
            require __DIR__ . '/includes/list-page-view-container.php';
            ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>