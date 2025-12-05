<?php
/**
 * Stories Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Story;

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
} elseif ($sort === 'popular') {
    $orderBy = 'likes DESC, comments DESC';
} elseif ($sort === 'title') {
    $orderBy = 'title ASC';
} else {
    $orderBy = 'created_at DESC';
}

// Get stories
$storyModel = new Story();
$stories = $storyModel->all($conditions, $orderBy, $perPage, $offset);
$totalStories = $storyModel->count($conditions);
$totalPages = ceil($totalStories / $perPage);

// Filter by search if provided
if ($search) {
    $stories = array_filter($stories, fn($story): bool => stripos((string) $story['title'], (string) $search) !== false ||
           stripos((string) $story['description'], (string) $search) !== false);
}

// Prepare table data
$tableData = array_values(prepare_table_data(
    array_map(fn($story): array => [
        'id' => $story['id'],
        'image' => $story['image'] ? getImageUrl($story['image']) : '',
        'title' => $story['title'],
        'author' => $story['author'] ?? 'Anonymous',
        'category' => $story['category'],
        'category_label' => ucfirst((string) $story['category']),
        'region' => 'East Africa', // Stories don't have region in DB
        'status' => $story['status'],
        'status_label' => ucfirst((string) $story['status']),
        'created_at' => $story['created_at'],
        'date_formatted' => formatDate($story['created_at']),
        'likes' => $story['likes'] ?? 0,
        'comments' => $story['comments'] ?? 0,
    ], $stories),
    BASE_URL . '/admin/story-edit.php?id={id}',
    BASE_URL . '/admin/story-delete.php?id={id}',
    BASE_URL . '/admin/story-view.php?id={id}'
));

// Define table columns
$tableColumns = [
    ['title' => 'Image', 'field' => 'image', 'formatter' => 'image', 'formatterParams' => ['height' => 60, 'width' => 60], 'width' => 100, 'headerSort' => false],
    ['title' => 'Title', 'field' => 'title', 'sorter' => 'string', 'width' => 200],
    ['title' => 'Author', 'field' => 'author', 'sorter' => 'string', 'width' => 120],
    ['title' => 'Category', 'field' => 'category_label', 'sorter' => 'string', 'width' => 120],
    ['title' => 'Likes', 'field' => 'likes', 'sorter' => 'number', 'width' => 80, 'hozAlign' => 'center'],
    ['title' => 'Comments', 'field' => 'comments', 'sorter' => 'number', 'width' => 80, 'hozAlign' => 'center'],
    ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
    ['title' => 'Created', 'field' => 'created_at', 'sorter' => 'date', 'width' => 150],
];

// Set page variables
$pageTitle = 'Stories Management';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Stories', 'url' => BASE_URL . '/admin/stories.php'],
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
            $pageTitle = 'All Stories';
            $addNewUrl = BASE_URL . '/admin/story-edit.php';
            $addNewLabel = 'Add New Story';
            $exportTableId = 'storiesTable';
            $showExport = true;
            $showViewSwitcher = true;
            $viewSwitcherKey = 'admin_stories_view';
            $defaultView = 'grid';
            require __DIR__ . '/includes/list-page-actions.php';
            ?>
            
            <?php
            // Filters configuration
            $showSearch = true;
            $searchValue = $search;
            $formId = 'storiesFilterForm';
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
                        'empowerment' => 'Empowerment'
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
                        'popular' => 'Most Popular',
                        'title' => 'Title A-Z'
                    ]
                ],
            ];
            require __DIR__ . '/includes/list-page-filters.php';
            ?>
            
            <?php
            // View container configuration
            $tableId = 'storiesTable';
            $gridTemplate = __DIR__ . '/includes/grid-templates/story-card.php';
            require __DIR__ . '/includes/list-page-view-container.php';
            ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
