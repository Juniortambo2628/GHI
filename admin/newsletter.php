<?php
/**
 * Newsletter Subscribers Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\NewsletterSubscriber;

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
    $orderBy = 'subscribed_at ASC';
} elseif ($sort === 'email') {
    $orderBy = 'email ASC';
} elseif ($sort === 'name') {
    $orderBy = 'name ASC';
} else {
    $orderBy = 'subscribed_at DESC';
}

// Get subscribers
$newsletterModel = new NewsletterSubscriber();
$subscribers = $newsletterModel->all($conditions, $orderBy, $perPage, $offset);
$totalSubscribers = $newsletterModel->count($conditions);
$totalPages = ceil($totalSubscribers / $perPage);

// Filter by date if provided
if ($dateFilter) {
    $today = date('Y-m-d');
    $weekAgo = date('Y-m-d', strtotime('-7 days'));
    $monthAgo = date('Y-m-d', strtotime('-30 days'));

    $subscribers = array_filter($subscribers, function (array $subscriber) use ($dateFilter, $today, $weekAgo, $monthAgo): bool {
        $subDate = date('Y-m-d', strtotime((string) $subscriber['subscribed_at']));
        if ($dateFilter === 'today') {
            return $subDate === $today;
        }

        if ($dateFilter === 'week') {
            return $subDate >= $weekAgo;
        }

        if ($dateFilter === 'month') {
            return $subDate >= $monthAgo;
        }

        return true;
    });
}

// Filter by search if provided
if ($search) {
    $subscribers = array_filter($subscribers, fn($subscriber): bool => stripos((string) $subscriber['email'], (string) $search) !== false ||
           stripos((string) $subscriber['name'], (string) $search) !== false);
}

// Set page variables
$pageTitle = 'Newsletter Subscribers';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Newsletter', 'url' => BASE_URL . '/admin/newsletter.php'],
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
                <h2 class="page-section-title">Newsletter Subscribers</h2>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" data-export-excel="newsletterTable" data-filename="newsletter-subscribers-export.xlsx" title="Export to Excel">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-danger" data-export-pdf="newsletterTable" data-filename="newsletter-subscribers-report.pdf" title="Export to PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </button>
                        <button type="button" class="btn btn-info" data-export-csv="newsletterTable" data-filename="newsletter-subscribers-export.csv" title="Export to CSV">
                            <i class="bi bi-filetype-csv me-1"></i>CSV
                        </button>
                    </div>
                    <button class="btn btn-primary" id="sendNewsletterBtn">
                        <i class="bi bi-envelope-paper me-2"></i>Send Newsletter
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
                                    <input type="text" name="search" class="form-control" placeholder="Search subscribers..." value="<?php echo e($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="unsubscribed" <?php echo $status === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
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
            
            <!-- Subscribers List -->
            <div class="card">
                <div class="card-body">
                    <div 
                        id="newsletterTable" 
                        data-tabulator 
                        data-table-data='<?php 
                        $tableData = prepare_table_data(
                            array_map(fn($subscriber): array => [
                                'id' => $subscriber['id'],
                                'email' => $subscriber['email'] ?? '',
                                'name' => $subscriber['name'] ?? '',
                                'status' => $subscriber['status'] ?? 'active',
                                'status_label' => ucfirst($subscriber['status'] ?? 'active'),
                                'subscribed_at' => $subscriber['subscribed_at'] ?? '',
                                'last_email_sent' => $subscriber['last_email_sent'] ?? null,
                                'last_email_sent_formatted' => $subscriber['last_email_sent'] ? formatDate($subscriber['last_email_sent']) : 'Never',
                            ], $subscribers),
                            BASE_URL . '/admin/newsletter-edit.php?id={id}',
                            BASE_URL . '/admin/newsletter-delete.php?id={id}'
                        );
                        echo json_encode(array_values($tableData), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
                        ?>'
                        data-columns='<?php 
                        echo get_tabulator_columns_json([
                            ['title' => 'Email', 'field' => 'email', 'sorter' => 'string', 'width' => 250],
                            ['title' => 'Name', 'field' => 'name', 'sorter' => 'string', 'width' => 150],
                            ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
                            ['title' => 'Subscribed', 'field' => 'subscribed_at', 'sorter' => 'date', 'width' => 150],
                            ['title' => 'Last Email', 'field' => 'last_email_sent_formatted', 'sorter' => 'string', 'width' => 150],
                        ]);
                        ?>'
                    ></div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

