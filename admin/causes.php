<?php
/**
 * Causes Management
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\Cause;

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
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
} elseif ($sort === 'title') {
    $orderBy = 'title ASC';
} else {
    $orderBy = 'created_at DESC';
}

// Get causes
$causeModel = new Cause();
$causes = $causeModel->all($conditions, $orderBy, $perPage, $offset);
$totalCauses = $causeModel->count($conditions);
$totalPages = ceil($totalCauses / $perPage);

// Filter by search if provided
if ($search) {
    $causes = array_filter($causes, fn($cause): bool => stripos((string) $cause['title'], (string) $search) !== false ||
           stripos((string) $cause['description'], (string) $search) !== false);
}

// Set page variables
$pageTitle = 'Causes Management';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Causes', 'url' => BASE_URL . '/admin/causes.php'],
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
                <h2 class="page-section-title">All Causes</h2>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" data-export-excel="causesTable" data-filename="causes-export.xlsx" title="Export to Excel">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-danger" data-export-pdf="causesTable" data-filename="causes-report.pdf" title="Export to PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </button>
                        <button type="button" class="btn btn-info" data-export-csv="causesTable" data-filename="causes-export.csv" title="Export to CSV">
                            <i class="bi bi-filetype-csv me-1"></i>CSV
                        </button>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/admin/cause-edit.php" class="btn btn-dark">
                        <i class="bi bi-plus-circle me-2"></i>Add New Cause
                    </a>
                </div>
            </div>
            
            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <form method="GET" action="" id="causesFilterForm" data-auto-submit>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search causes..." value="<?php echo e($search); ?>">
                                </div>
                                <div class="col-md-4">
                                    <select name="status" class="form-select" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="sort" class="form-select" id="sortFilter">
                                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                                        <option value="title" <?php echo $sort === 'title' ? 'selected' : ''; ?>>Title A-Z</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <?php
            $tableData = array_values(prepare_table_data(
                array_map(fn($cause): array => [
                    'id' => $cause['id'],
                    'image' => $cause['image'] ? getImageUrl($cause['image']) : '',
                    'title' => $cause['title'],
                    'slug' => $cause['slug'],
                    'display_order' => (int)$cause['display_order'],
                    'status' => $cause['status'],
                    'status_label' => ucfirst((string) $cause['status']),
                    'created_at' => $cause['created_at'],
                ], $causes),
                BASE_URL . '/admin/cause-edit.php?id={id}',
                BASE_URL . '/admin/cause-delete.php?id={id}',
                BASE_URL . '/admin/cause-view.php?id={id}'
            ));
            ?>

            <div class="d-flex justify-content-end mb-3">
                <div class="view-switcher" data-view-key="admin_causes_view" data-default-view="table">
                    <button type="button" class="btn btn-outline-primary view-switcher__btn active" data-view-mode="table" aria-label="Table view">
                        <i class="bi bi-table"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary view-switcher__btn" data-view-mode="grid" aria-label="Grid view">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                </div>
            </div>

            <div data-view-container="admin_causes_view">
                <section class="view-section" data-view-mode="table">
                    <div class="card">
                        <div class="card-body">
                            <div 
                                id="causesTable" 
                                data-tabulator 
                                data-sortable="true"
                                data-entity-type="causes"
                                class="tabulator-container"
                                data-table-data='<?php echo json_encode($tableData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES); ?>'
                                data-columns='<?php 
                                echo get_tabulator_columns_json([
                                    ['title' => 'Image', 'field' => 'image', 'formatter' => 'image', 'formatterParams' => ['height' => 60, 'width' => 60], 'width' => 100, 'headerSort' => false],
                                    ['title' => 'Title', 'field' => 'title', 'sorter' => 'string', 'width' => 200],
                                    ['title' => 'Slug', 'field' => 'slug', 'sorter' => 'string', 'width' => 150],
                                    ['title' => 'Order', 'field' => 'display_order', 'sorter' => 'number', 'width' => 100, 'hozAlign' => 'center'],
                                    ['title' => 'Status', 'field' => 'status_label', 'sorter' => 'string', 'width' => 100],
                                    ['title' => 'Created', 'field' => 'created_at', 'sorter' => 'date', 'width' => 150],
                                ]);
                                ?>'
                            ></div>
                        </div>
                    </div>
                </section>

                <section class="view-section d-none" data-view-mode="grid">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-4" data-sortable-grid="true" data-entity-type="causes">
                                <?php if ($tableData !== []): ?>
                                    <?php foreach ($tableData as $cause): 
                                        $actionsJson = htmlspecialchars(json_encode($cause['action_menu'] ?? []), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6" data-item-id="<?php echo $cause['id']; ?>">
                                            <div class="admin-grid-card h-100" data-action-menu="<?php echo $actionsJson; ?>">
                                                <?php if (!empty($cause['image'])): ?>
                                                    <img src="<?php echo $cause['image']; ?>" alt="<?php echo e($cause['title']); ?>">
                                                <?php else: ?>
                                                    <div class="bg-light text-center py-5 fw-semibold text-muted">No Image</div>
                                                <?php endif;
                                         ?>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="mb-0"><?php echo e($cause['title']); ?></h5>
                                                        <button type="button" class="action-menu-trigger" data-action-menu="<?php echo $actionsJson; ?>" aria-label="Open actions">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                    </div>
                                                    <p class="card-meta mb-2">Slug: <?php echo e($cause['slug']); ?></p>
                                                    <span class="status-chip <?php echo $cause['status'] === 'inactive' ? 'is-draft' : ''; ?>">
                                                        <?php echo e($cause['status_label'] ?? ucfirst((string) $cause['status'])); ?>
                                                    </span>
                                                    <p class="card-meta mt-3 mb-0">Display Order: <?php echo e($cause['display_order']); ?></p>
                                                    <p class="card-meta mb-0">Created: <?php echo e($cause['created_at']); ?></p>
                                                </div>
                                            </div>
                                        </div>
<?php endforeach;
                                 ?>
<?php else: ?>
                                    <div class="col-12">
                                        <p class="text-center text-muted mb-0">No causes found.</p>
                                    </div>
                                <?php endif;
                                 ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

