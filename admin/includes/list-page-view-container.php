<?php
/**
 * Admin List Page View Container
 * Reusable component for admin list pages with grid/table views
 * 
 * Required variables:
 * - $viewSwitcherKey: string - Key for view switcher storage
 * - $tableId: string - ID for the Tabulator table
 * - $tableData: array - Prepared table data
 * - $tableColumns: array - Column configuration for Tabulator
 * - $gridTemplate: string - Path to grid template file (optional)
 * - $gridData: array - Data for grid view (defaults to $tableData)
 * - $defaultView: string - Default view mode: "grid" or "table" (default: "table")
 * - $entityType: string - Entity type for sortable (optional)
 */

$defaultView ??= 'table';
$gridData ??= $tableData;
$entityType ??= '';
?>

<div data-view-container="<?php echo e($viewSwitcherKey); ?>">
    <!-- Table View -->
    <div class="card view-section <?php echo $defaultView !== 'table' ? 'd-none' : ''; ?>" data-view-mode="table">
        <div class="card-body">
            <div 
                id="<?php echo e($tableId); ?>" 
                data-tabulator 
                data-row-action="view"
                <?php if ($entityType): ?>
                data-sortable="true"
                data-entity-type="<?php echo e($entityType); ?>"
                <?php endif;
 ?>
                data-table-data='<?php echo json_encode($tableData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES); ?>'
                data-columns='<?php echo get_tabulator_columns_json($tableColumns); ?>'
            ></div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="card view-section <?php echo $defaultView !== 'grid' ? 'd-none' : ''; ?>" data-view-mode="grid">
        <div class="card-body">
            <div class="row g-4" <?php echo $entityType ? 'data-sortable-grid="true" data-entity-type="' . e($entityType) . '"' : ''; ?>>
                <?php if (!empty($gridData)): ?>
                    <?php 
                    // Include custom grid template if provided
                    if (isset($gridTemplate) && file_exists($gridTemplate)) {
                        foreach ($gridData as $item) {
                            include $gridTemplate;
                        }
                    } else {
                        // Default grid card template
                        foreach ($gridData as $item):
                            $actionsJson = htmlspecialchars(json_encode($item['action_menu'] ?? []), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="col-xl-3 col-lg-4 col-md-6" data-item-id="<?php echo $item['id']; ?>">
                        <div class="admin-grid-card h-100">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title'] ?? 'Item'); ?>">
                            <?php else: ?>
                                <div class="bg-light text-center py-5 fw-semibold text-muted">No Image</div>
                            <?php endif;
                     ?>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0"><?php echo e($item['title'] ?? 'Untitled'); ?></h5>
                                    <button type="button" class="action-menu-trigger" data-action-menu="<?php echo $actionsJson; ?>" aria-label="Open actions">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </div>
                                <?php if (isset($item['status_label'])): ?>
                                <span class="status-chip <?php echo ($item['status'] ?? 'active') === 'inactive' || ($item['status'] ?? 'published') === 'draft' ? 'is-draft' : ''; ?>">
                                    <?php echo e($item['status_label']); ?>
                                </span>
                                <?php endif;
                     ?>
                                <?php if (isset($item['created_at'])): ?>
                                <p class="card-meta mt-3 mb-0">Created: <?php echo e($item['created_at']); ?></p>
                                <?php endif;
                     ?>
                            </div>
                        </div>
                    </div>
<?php endforeach; 
                    }
                     ?>
<?php else: ?>
                    <div class="col-12">
                        <p class="text-center text-muted mb-0">No items found.</p>
                    </div>
                <?php endif;
 ?>
            </div>
        </div>
    </div>
</div>
