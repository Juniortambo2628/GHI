<?php
/**
 * Events Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Event;
use GHI\Models\Initiative;

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$initiativeId = $_GET['initiative'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build conditions
$conditions = [];
if ($status) {
    $conditions['status'] = $status;
}

if ($initiativeId) {
    $conditions['initiative_id'] = (int)$initiativeId;
}

// Build order by
if ($sort === 'oldest') {
    $orderBy = 'created_at ASC';
} elseif ($sort === 'date') {
    $orderBy = 'event_date ASC';
} elseif ($sort === 'title') {
    $orderBy = 'title ASC';
} else {
    $orderBy = 'created_at DESC';
}

// Get events
$eventModel = new Event();
$events = $eventModel->all($conditions, $orderBy, $perPage, $offset);
$totalEvents = $eventModel->count($conditions);
$totalPages = ceil($totalEvents / $perPage);

// Filter by date if provided
if ($dateFilter === 'upcoming') {
    $events = array_filter($events, fn($event): bool => strtotime((string) $event['event_date']) >= strtotime('today'));
} elseif ($dateFilter === 'past') {
    $events = array_filter($events, fn($event): bool => strtotime((string) $event['event_date']) < strtotime('today'));
}

// Filter by search if provided
if ($search) {
    $events = array_filter($events, fn($event): bool => stripos((string) $event['title'], (string) $search) !== false ||
           stripos((string) $event['description'], (string) $search) !== false ||
           stripos((string) $event['location'], (string) $search) !== false);
}

// Get initiatives for filter dropdown
$initiativeModel = new Initiative();
$allInitiatives = $initiativeModel->all(['status' => 'published'], 'title ASC');
$initiativesLookup = [];
foreach ($allInitiatives as $init) {
    $initiativesLookup[$init['id']] = $init['title'];
}

// Prepare table data
$tableData = array_values(prepare_table_data(
    array_map(fn($event): array => [
        'id' => $event['id'],
        'image' => $event['image'] ? getImageUrl($event['image']) : '',
        'title' => $event['title'],
        'initiative' => $initiativesLookup[$event['initiative_id']] ?? 'N/A',
        'event_date' => $event['event_date'],
        'event_date_formatted' => formatDate($event['event_date']),
        'event_time' => date('g:i A', strtotime((string) $event['event_date'])),
        'location' => $event['location'],
        'status' => $event['status'],
        'status_label' => ucfirst((string) $event['status']),
    ], $events),
    BASE_URL . '/admin/event-edit.php?id={id}',
    BASE_URL . '/admin/event-delete.php?id={id}',
    BASE_URL . '/admin/event-view.php?id={id}'
));

// Define table columns
$tableColumns = [
    ['title' => 'Image', 'field' => 'image', 'formatter' => 'image', 'formatterParams' => ['height' => 60, 'width' => 60], 'width' => 100, 'headerSort' => false],
    ['title' => 'Title', 'field' => 'title', 'sorter' => 'string', 'width' => 200],
    ['title' => 'Initiative', 'field' => 'initiative', 'sorter' => 'string', 'width' => 150],
    ['title' => 'Date & Time', 'field' => 'event_date', 'sorter' => 'date', 'width' => 150, 'formatter' => 'datetime', 'formatterParams' => ['inputFormat' => 'YYYY-MM-DD HH:mm:ss', 'outputFormat' => 'MMM D, YYYY h:mm A']],
    ['title' => 'Location', 'field' => 'location', 'sorter' => 'string', 'width' => 150],
    ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
];

// Set page variables
$pageTitle = 'Events Management';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Events', 'url' => BASE_URL . '/admin/events.php'],
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
            $pageTitle = 'All Events';
            $addNewUrl = BASE_URL . '/admin/event-edit.php';
            $addNewLabel = 'Add New Event';
            $exportTableId = 'eventsTable';
            $showExport = true;
            $showViewSwitcher = true;
            $viewSwitcherKey = 'admin_events_view';
            $defaultView = 'grid';
            require __DIR__ . '/includes/list-page-actions.php';
            ?>
            
            <?php
            // Filters configuration
            $showSearch = true;
            $searchValue = $search;
            $formId = 'eventsFilterForm';
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
                    'name' => 'date',
                    'placeholder' => 'All Dates',
                    'value' => $dateFilter,
                    'options' => ['upcoming' => 'Upcoming', 'past' => 'Past']
                ],
                [
                    'type' => 'select',
                    'name' => 'initiative',
                    'placeholder' => 'All Initiatives',
                    'value' => $initiativeId,
                    'options' => $initiativesLookup
                ],
            ];
            require __DIR__ . '/includes/list-page-filters.php';
            ?>
            
            <?php
            // View container configuration
            $tableId = 'eventsTable';
            $gridTemplate = __DIR__ . '/includes/grid-templates/event-card.php';
            require __DIR__ . '/includes/list-page-view-container.php';
            ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
