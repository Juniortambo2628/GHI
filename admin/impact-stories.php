<?php
/**
 * Impact Stories Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Impact;

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$objective = $_GET['objective'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build conditions
$conditions = [];
if ($status) {
    $conditions['status'] = $status;
}

if ($objective) {
    $conditions['core_objective'] = $objective;
}

// Build order by
if ($sort === 'oldest') {
    $orderBy = 'created_at ASC';
} elseif ($sort === 'title') {
    $orderBy = 'title ASC';
} else {
    $orderBy = 'created_at DESC';
}

// Get impact stories
$impactModel = new Impact();
$impacts = $impactModel->all($conditions, $orderBy, $perPage, $offset);
$totalImpacts = $impactModel->count($conditions);
$totalPages = ceil($totalImpacts / $perPage);

// Filter by search if provided
if ($search) {
    $impacts = array_filter($impacts, fn($impact): bool => stripos((string) $impact['title'], (string) $search) !== false ||
           stripos((string) $impact['description'], (string) $search) !== false);
}

// Prepare table data
$tableData = array_values(prepare_table_data(
    array_map(fn($impact): array => [
        'id' => $impact['id'],
        'image' => $impact['image'] ? getImageUrl($impact['image']) : '',
        'title' => $impact['title'],
        'region' => $impact['region'] ?? 'East Africa',
        'core_objective' => $impact['core_objective'] ?? 'Community Development',
        'beneficiaries' => $impact['beneficiaries'] ?? 0,
        'status' => $impact['status'],
        'status_label' => ucfirst((string) $impact['status']),
        'created_at' => $impact['created_at'],
    ], $impacts),
    BASE_URL . '/admin/impact-edit.php?id={id}',
    BASE_URL . '/admin/impact-delete.php?id={id}',
    BASE_URL . '/admin/impact-view.php?id={id}'
));

// Define table columns
$tableColumns = [
    ['title' => 'Image', 'field' => 'image', 'formatter' => 'image', 'formatterParams' => ['height' => 60, 'width' => 60], 'width' => 100, 'headerSort' => false],
    ['title' => 'Title', 'field' => 'title', 'sorter' => 'string', 'width' => 200],
    ['title' => 'Region', 'field' => 'region', 'sorter' => 'string', 'width' => 120],
    ['title' => 'Objective', 'field' => 'core_objective', 'sorter' => 'string', 'width' => 180],
    ['title' => 'Beneficiaries', 'field' => 'beneficiaries', 'sorter' => 'number', 'width' => 120, 'hozAlign' => 'center'],
    ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
    ['title' => 'Created', 'field' => 'created_at', 'sorter' => 'date', 'width' => 150],
];

// Set page variables
$pageTitle = 'Impact Stories Management';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Impact Stories', 'url' => BASE_URL . '/admin/impact-stories.php'],
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
            $pageTitle = 'All Impact Stories';
            $addNewUrl = BASE_URL . '/admin/impact-edit.php';
            $addNewLabel = 'Add New Impact Story';
            $exportTableId = 'impactStoriesTable';
            $showExport = true;
            $showViewSwitcher = true;
            $viewSwitcherKey = 'admin_impact_view';
            $defaultView = 'grid';
            require __DIR__ . '/includes/list-page-actions.php';
            ?>
            
            <?php
            // Filters configuration
            $showSearch = true;
            $searchValue = $search;
            $formId = 'impactFilterForm';
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
                    'name' => 'objective',
                    'placeholder' => 'All Objectives',
                    'value' => $objective,
                    'options' => [
                        'poverty' => 'Poverty Alleviation',
                        'education' => 'Education Access',
                        'health' => 'Health & Well-being',
                        'empowerment' => 'Community Empowerment',
                        'partnerships' => 'Global Partnerships'
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
            $tableId = 'impactStoriesTable';
            $gridTemplate = __DIR__ . '/includes/grid-templates/impact-card.php';
            require __DIR__ . '/includes/list-page-view-container.php';
            ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
