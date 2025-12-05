<?php
/**
 * Donations Admin Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Donation;

// Check authentication
require_login();

// Get donations model
$donationModel = new Donation();

// Get current page
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Get donations with pagination
$donationsData = $donationModel->getAllWithPagination($currentPage, 20);
$donations = $donationsData['items'];
$totalPages = $donationsData['totalPages'];

// Get statistics
$stats = $donationModel->getStatistics();

// Set page variables
$pageTitle = 'Donations';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Donations', 'url' => BASE_URL . '/admin/donations.php'],
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
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="summary-content ms-3">
                                    <h3 class="summary-number">$<?php echo number_format($stats['total_amount'] ?? 0, 2); ?></h3>
                                    <p class="summary-label">Total Raised</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="summary-content ms-3">
                                    <h3 class="summary-number"><?php echo number_format($stats['total_donations'] ?? 0); ?></h3>
                                    <p class="summary-label">Total Donations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <div class="summary-content ms-3">
                                    <h3 class="summary-number">$<?php echo number_format($stats['avg_amount'] ?? 0, 2); ?></h3>
                                    <p class="summary-label">Average Donation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon">
                                    <i class="bi bi-arrow-repeat"></i>
                                </div>
                                <div class="summary-content ms-3">
                                    <h3 class="summary-number"><?php echo number_format($stats['recurring_donations'] ?? 0); ?></h3>
                                    <p class="summary-label">Recurring Donors</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donations Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Donations</h5>
                    <a href="<?php echo BASE_URL; ?>/donate.php" class="btn btn-sm btn-primary" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i>View Donate Page
                    </a>
                </div>
                <div class="card-body">
                    <div 
                        id="donationsTable"
                        data-tabulator
                        data-table-data='<?php
                        $donationsData = prepare_table_data(
                            array_map(fn($donation): array => [
                                'id' => $donation['id'],
                                'name' => $donation['firstname'] . ' ' . $donation['lastname'],
                                'email' => $donation['email'],
                                'amount' => '$' . number_format($donation['amount'], 2),
                                'type' => ucfirst(str_replace('_', ' ', $donation['donation_type'])),
                                'status' => $donation['status'],
                                'status_label' => ucfirst((string) $donation['status']),
                                'created_at' => $donation['created_at'],
                            ], $donations)
                        );
                        echo json_encode(array_values($donationsData), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
                        ?>'
                        data-columns='<?php
                        echo get_tabulator_columns_json([
                            ['title' => 'ID', 'field' => 'id', 'sorter' => 'number', 'width' => 80],
                            ['title' => 'Donor Name', 'field' => 'name', 'sorter' => 'string', 'width' => 200],
                            ['title' => 'Email', 'field' => 'email', 'sorter' => 'string', 'width' => 250],
                            ['title' => 'Amount', 'field' => 'amount', 'sorter' => 'string', 'width' => 120],
                            ['title' => 'Type', 'field' => 'type', 'sorter' => 'string', 'width' => 120],
                            ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 120],
                            ['title' => 'Date', 'field' => 'created_at', 'sorter' => 'date', 'width' => 180, 'formatter' => 'datetime', 'formatterParams' => ['inputFormat' => 'YYYY-MM-DD HH:mm:ss', 'outputFormat' => 'MMM D, YYYY h:mm A']],
                        ]);
                        ?>'
                    ></div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
