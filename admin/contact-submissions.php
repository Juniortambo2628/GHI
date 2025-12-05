<?php
/**
 * Contact Submissions Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\ContactSubmission;

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build conditions
$conditions = [];
if ($status) {
    $conditions['status'] = $status;
}

// Build order by
if ($sort === 'oldest') {
    $orderBy = 'created_at ASC';
} elseif ($sort === 'name') {
    $orderBy = 'firstname ASC, lastname ASC';
} else {
    $orderBy = 'created_at DESC';
}

// Get contact submissions
$contactModel = new ContactSubmission();
$contacts = $contactModel->all($conditions, $orderBy, $perPage, $offset);
$totalContacts = $contactModel->count($conditions);
$totalPages = ceil($totalContacts / $perPage);

// Filter by date if provided
if ($dateFilter) {
    $today = date('Y-m-d');
    $weekAgo = date('Y-m-d', strtotime('-7 days'));
    $monthAgo = date('Y-m-d', strtotime('-30 days'));

    $contacts = array_filter($contacts, function (array $contact) use ($dateFilter, $today, $weekAgo, $monthAgo): bool {
        $contactDate = date('Y-m-d', strtotime((string) $contact['created_at']));
        if ($dateFilter === 'today') {
            return $contactDate === $today;
        }
        
        if ($dateFilter === 'week') {
            return $contactDate >= $weekAgo;
        }

        if ($dateFilter === 'month') {
            return $contactDate >= $monthAgo;
        }

        return true;
    });
}

// Filter by search if provided
if ($search) {
    $contacts = array_filter($contacts, fn($contact): bool => stripos($contact['firstname'] . ' ' . $contact['lastname'], (string) $search) !== false ||
           stripos((string) $contact['email'], (string) $search) !== false ||
           stripos((string) $contact['message'], (string) $search) !== false);
}

// Set page variables
$pageTitle = 'Contact Submissions';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Contact Submissions', 'url' => BASE_URL . '/admin/contact-submissions.php'],
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
            <!-- Page Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-section-title">Contact Submissions</h2>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" data-export-excel="contactSubmissionsTable" data-filename="contact-submissions-export.xlsx" title="Export to Excel">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-danger" data-export-pdf="contactSubmissionsTable" data-filename="contact-submissions-report.pdf" title="Export to PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </button>
                        <button type="button" class="btn btn-info" data-export-csv="contactSubmissionsTable" data-filename="contact-submissions-export.csv" title="Export to CSV">
                            <i class="bi bi-filetype-csv me-1"></i>CSV
                        </button>
                    </div>
                    <button class="btn btn-secondary" id="markAllRead">
                        <i class="bi bi-check-all me-2"></i>Mark All as Read
                    </button>
                </div>
            </div>
            
            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <form method="GET" action="">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search submissions..." value="<?php echo e($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="new" <?php echo $status === 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="read" <?php echo $status === 'read' ? 'selected' : ''; ?>>Read</option>
                                        <option value="replied" <?php echo $status === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="date" class="form-select" id="dateFilter">
                                        <option value="">All Dates</option>
                                        <option value="today" <?php echo $dateFilter === 'today' ? 'selected' : ''; ?>>Today</option>
                                        <option value="week" <?php echo $dateFilter === 'week' ? 'selected' : ''; ?>>This Week</option>
                                        <option value="month" <?php echo $dateFilter === 'month' ? 'selected' : ''; ?>>This Month</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-dark w-100">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Submissions List -->
            <div class="card">
                <div class="card-body">
                    <div 
                        id="contactSubmissionsTable" 
                        data-tabulator 
                        data-row-action="view"
                        data-table-data='<?php 
                        $tableData = prepare_table_data(
                            array_map(function($contact): array {
                                $name = trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''));
                                if (($name === '' || $name === '0') && isset($contact['name'])) {
                                    $name = $contact['name'];
                                }
                                
                                $statusColors = [
                                    'new' => 'primary',
                                    'read' => 'info',
                                    'replied' => 'success',
                                ];
                                return [
                                    'id' => $contact['id'],
                                    'name' => $name,
                                    'email' => $contact['email'] ?? '',
                                    'subject' => $contact['subject'] ?? '',
                                    'message' => $contact['message'] ?? '',
                                    'message_preview' => truncate($contact['message'] ?? '', 50),
                                    'status' => $contact['status'] ?? 'new',
                                    'status_label' => ucfirst($contact['status'] ?? 'new'),
                                    'status_color' => $statusColors[$contact['status'] ?? 'new'] ?? 'secondary',
                                    'created_at' => $contact['created_at'] ?? '',
                                ];
                            }, $contacts),
                            null,
                            BASE_URL . '/admin/contact-delete.php?id={id}',
                            BASE_URL . '/admin/contact-view.php?id={id}'
                        );
                        echo json_encode(array_values($tableData), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
                        ?>'
                        data-columns='<?php 
                        echo get_tabulator_columns_json([
                            ['title' => 'Name', 'field' => 'name', 'sorter' => 'string', 'width' => 150],
                            ['title' => 'Email', 'field' => 'email', 'sorter' => 'string', 'width' => 200],
                            ['title' => 'Subject', 'field' => 'subject', 'sorter' => 'string', 'width' => 150, 'formatter' => 'plaintext'],
                            ['title' => 'Message', 'field' => 'message_preview', 'sorter' => 'string', 'width' => 200, 'formatter' => 'plaintext'],
                            ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
                            ['title' => 'Date', 'field' => 'created_at', 'sorter' => 'date', 'width' => 150, 'formatter' => 'datetime', 'formatterParams' => ['inputFormat' => 'YYYY-MM-DD HH:mm:ss', 'outputFormat' => 'MMM D, YYYY']],
                        ]);
                        ?>'
                    ></div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

