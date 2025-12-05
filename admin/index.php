<?php
/**
 * Admin Dashboard
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Cause;
use GHI\Models\ContactSubmission;
use GHI\Models\Event;
use GHI\Models\ImpactActivity;
use GHI\Models\Initiative;
use GHI\Models\NewsletterSubscriber;

// Get statistics
$causeModel = new Cause();
$initiativeModel = new Initiative();
$eventModel = new Event();
$impactModel = new ImpactActivity();
$contactModel = new ContactSubmission();
$newsletterModel = new NewsletterSubscriber();

$stats = [
    'causes' => $causeModel->count(),
    'initiatives' => $initiativeModel->count(),
    'events' => $eventModel->count(),
    'impact' => $impactModel->count(),
    'contacts' => $contactModel->count(['status' => 'new']),
    'subscribers' => $newsletterModel->count(['status' => 'active']),
];

// Get recent contact submissions
$recentContacts = $contactModel->all([], 'created_at DESC', 5);

// Prepare chart data
// Initiatives by Status
$initiativesByStatus = $initiativeModel->all([], 'status ASC');
$initiativesStatusData = [
    'labels' => ['Published', 'Draft', 'Archived'],
    'datasets' => [[
        'label' => 'Initiatives',
        'data' => [
            count(array_filter($initiativesByStatus, fn($i): bool => $i['status'] === 'published')),
            count(array_filter($initiativesByStatus, fn($i): bool => $i['status'] === 'draft')),
            count(array_filter($initiativesByStatus, fn($i): bool => $i['status'] === 'archived')),
        ],
        'backgroundColor' => ['#c0a85b', '#e0ddcf', '#9E9E9E'],
    ]],
];

// Events by Status
$eventsByStatus = $eventModel->all([], 'status ASC');
$eventsStatusData = [
    'labels' => ['Published', 'Draft', 'Completed'],
    'datasets' => [[
        'label' => 'Events',
        'data' => [
            count(array_filter($eventsByStatus, fn($e): bool => $e['status'] === 'published')),
            count(array_filter($eventsByStatus, fn($e): bool => $e['status'] === 'draft')),
            count(array_filter($eventsByStatus, fn($e): bool => $e['status'] === 'completed')),
        ],
        'backgroundColor' => ['#c0a85b', '#e0ddcf', '#212121'],
    ]],
];

// Initiatives by Core Objective (simplified - using cause_id as proxy)
$initiativesByObjective = [];
foreach ($initiativesByStatus as $initiative) {
    $objective = $initiative['core_objective'] ?? 'Other';
    $initiativesByObjective[$objective] = ($initiativesByObjective[$objective] ?? 0) + 1;
}

$objectivesData = [
    'labels' => array_keys($initiativesByObjective),
    'datasets' => [[
        'label' => 'Initiatives',
        'data' => array_values($initiativesByObjective),
        'backgroundColor' => ['#c0a85b', '#212121', '#e0ddcf', '#9E9E9E', '#666666'],
    ]],
];

// Events Trend (last 6 months) - simplified
$eventsTrendData = [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'datasets' => [[
        'label' => 'Events',
        'data' => [5, 8, 6, 10, 7, 9], // Placeholder - should be calculated from actual data
        'borderColor' => '#c0a85b',
        'backgroundColor' => 'rgba(192, 168, 91, 0.1)',
        'fill' => true,
    ]],
];

// Impact Trend (last 6 months) - simplified
$impactTrendData = [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'datasets' => [[
        'label' => 'Impact Activities',
        'data' => [3, 5, 4, 7, 6, 8], // Placeholder - should be calculated from actual data
        'borderColor' => '#212121',
        'backgroundColor' => 'rgba(33, 33, 33, 0.1)',
        'fill' => true,
    ]],
];

// Set page variables
$pageTitle = 'Admin Dashboard';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
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
            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-heart"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-number"><?php echo number_format($stats['causes']); ?></h3>
                            <p class="summary-label">Causes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-number"><?php echo number_format($stats['initiatives']); ?></h3>
                            <p class="summary-label">Initiatives</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-number"><?php echo number_format($stats['events']); ?></h3>
                            <p class="summary-label">Events</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-number"><?php echo number_format($stats['impact']); ?></h3>
                            <p class="summary-label">Impact</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-number"><?php echo number_format($stats['contacts']); ?></h3>
                            <p class="summary-label">Contact Messages</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="bi bi-mailbox"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-number"><?php echo number_format($stats['subscribers']); ?></h3>
                            <p class="summary-label">Newsletter Subscribers</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="chart-card">
                        <h5 class="chart-title">Initiatives by Status</h5>
                        <div class="chart-container">
                            <canvas 
                                id="initiativesStatusChart"
                                data-chart="pie"
                                data-chart-data='<?php echo json_encode($initiativesStatusData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-chart-options='{"title":"Initiatives by Status"}'
                            ></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-card">
                        <h5 class="chart-title">Events by Status</h5>
                        <div class="chart-container">
                            <canvas 
                                id="eventsStatusChart"
                                data-chart="doughnut"
                                data-chart-data='<?php echo json_encode($eventsStatusData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-chart-options='{"title":"Events by Status"}'
                            ></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-card">
                        <h5 class="chart-title">Initiatives by Core Objective</h5>
                        <div class="chart-container">
                            <canvas 
                                id="objectivesChart"
                                data-chart="bar"
                                data-chart-data='<?php echo json_encode($objectivesData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-chart-options='{"title":"Initiatives by Core Objective"}'
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="chart-card">
                        <h5 class="chart-title">Events Trend (Last 6 Months)</h5>
                        <div class="chart-container">
                            <canvas 
                                id="eventsTrendChart"
                                data-chart="line"
                                data-chart-data='<?php echo json_encode($eventsTrendData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-chart-options='{"title":"Events Trend"}'
                            ></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <h5 class="chart-title">Impact Trend (Last 6 Months)</h5>
                        <div class="chart-container">
                            <canvas 
                                id="impactTrendChart"
                                data-chart="area"
                                data-chart-data='<?php echo json_encode($impactTrendData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>'
                                data-chart-options='{"title":"Impact Trend"}'
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table and Quick Actions -->
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="table-card">
                        <h5 class="table-title">Recent Contact Submissions</h5>
                        <div 
                            id="recentContactsTable"
                            data-tabulator
                            data-row-action="view"
                            data-table-data='<?php 
                            $contactsData = prepare_table_data(
                                array_map(function($contact): array {
                                    $name = trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''));
                                    if (($name === '' || $name === '0') && isset($contact['name'])) {
                                        $name = $contact['name'];
                                    }
                                    
                                    return [
                                        'id' => $contact['id'],
                                        'name' => $name,
                                        'email' => $contact['email'] ?? '',
                                        'subject' => $contact['subject'] ?? '',
                                        'status' => $contact['status'] ?? 'new',
                                        'status_label' => ucfirst($contact['status'] ?? 'new'),
                                        'created_at' => $contact['created_at'] ?? '',
                                    ];
                                }, $recentContacts),
                                null,
                                null,
                                BASE_URL . '/admin/contact-view.php?id={id}'
                            );
                            echo json_encode(array_values($contactsData), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
                            ?>'
                            data-columns='<?php 
                            echo get_tabulator_columns_json([
                                ['title' => 'Name', 'field' => 'name', 'sorter' => 'string', 'width' => 150],
                                ['title' => 'Email', 'field' => 'email', 'sorter' => 'string', 'width' => 200],
                                ['title' => 'Subject', 'field' => 'subject', 'sorter' => 'string', 'width' => 200, 'formatter' => 'plaintext'],
                                ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
                                ['title' => 'Date', 'field' => 'created_at', 'sorter' => 'date', 'width' => 150, 'formatter' => 'datetime', 'formatterParams' => ['inputFormat' => 'YYYY-MM-DD HH:mm:ss', 'outputFormat' => 'MMM D, YYYY']],
                            ]);
                            ?>'
                        ></div>
                        <div class="table-responsive d-none">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recentContacts === []): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No contact submissions found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentContacts as $contact): ?>
                                            <tr>
                                                <td><?php echo e($contact['firstname'] . ' ' . $contact['lastname']); ?></td>
                                                <td><?php echo e($contact['email']); ?></td>
                                                <td><?php echo e(truncate($contact['message'] ?? '', 50)); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php
                                                        echo $contact['status'] === 'new' ? 'warning' :
                                                            ($contact['status'] === 'replied' ? 'success' : 'secondary');
                                            ?>">
                                                        <?php echo ucfirst((string) $contact['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($contact['created_at'], 'M j, Y'); ?></td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>/admin/contact-submissions.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-dark">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                             ?>
<?php endif;
                             ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="quick-actions-card">
                        <h5 class="quick-actions-title">Quick Actions</h5>
                        <div class="quick-actions-grid">
                            <a href="<?php echo BASE_URL; ?>/admin/causes.php" class="quick-action-item">
                                <i class="bi bi-heart"></i>
                                <span>Causes</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/initiatives.php" class="quick-action-item">
                                <i class="bi bi-lightbulb"></i>
                                <span>Initiatives</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/events.php" class="quick-action-item">
                                <i class="bi bi-calendar-event"></i>
                                <span>Events</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/impact-stories.php" class="quick-action-item">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Impact</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/stories.php" class="quick-action-item">
                                <i class="bi bi-journal-text"></i>
                                <span>Stories</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/contact-submissions.php" class="quick-action-item">
                                <i class="bi bi-envelope"></i>
                                <span>Contact</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/newsletter.php" class="quick-action-item">
                                <i class="bi bi-mailbox"></i>
                                <span>Newsletter</span>
                            </a>
                            <a href="<?php echo BASE_URL; ?>/admin/settings.php" class="quick-action-item">
                                <i class="bi bi-gear"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

