<?php
/**
 * Admin List Page Actions Bar
 * Reusable component for admin list pages
 * 
 * Required variables:
 * - $pageTitle: string - Title for the page
 * - $addNewUrl: string - URL for the "Add New" button
 * - $addNewLabel: string - Label for the "Add New" button (default: "Add New")
 * - $exportTableId: string - ID of the table for export (default: "dataTable")
 * - $showExport: bool - Whether to show export buttons (default: true)
 * - $showViewSwitcher: bool - Whether to show view switcher (default: false)
 * - $viewSwitcherKey: string - Key for view switcher storage (default: "admin_view")
 * - $defaultView: string - Default view mode: "grid" or "table" (default: "table")
 */

$addNewLabel ??= 'Add New';
$exportTableId ??= 'dataTable';
$showExport ??= true;
$showViewSwitcher ??= false;
$viewSwitcherKey ??= 'admin_view';
$defaultView ??= 'table';
?>

<!-- Page Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-section-title"><?php echo e($pageTitle); ?></h2>
    <div class="d-flex align-items-center gap-2">
        <?php if ($showViewSwitcher): ?>
        <div class="view-switcher me-2" data-view-key="<?php echo e($viewSwitcherKey); ?>" data-default-view="<?php echo e($defaultView); ?>">
            <button type="button" class="view-switcher__btn <?php echo $defaultView === 'table' ? 'active' : ''; ?>" data-view-mode="table" aria-label="Table view">
                <i class="bi bi-table"></i>
            </button>
            <button type="button" class="view-switcher__btn <?php echo $defaultView === 'grid' ? 'active' : ''; ?>" data-view-mode="grid" aria-label="Grid view">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
        </div>
        <?php endif;
 ?>
        
        <?php if ($showExport): ?>
        <div class="btn-group me-2" role="group">
            <button type="button" class="btn btn-success" data-export-excel="<?php echo e($exportTableId); ?>" data-filename="<?php echo e($exportTableId); ?>-export.xlsx" title="Export to Excel">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </button>
            <button type="button" class="btn btn-danger" data-export-pdf="<?php echo e($exportTableId); ?>" data-filename="<?php echo e($exportTableId); ?>-report.pdf" title="Export to PDF">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </button>
            <button type="button" class="btn btn-info" data-export-csv="<?php echo e($exportTableId); ?>" data-filename="<?php echo e($exportTableId); ?>-export.csv" title="Export to CSV">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </button>
        </div>
        <?php endif;
 ?>
        
        <a href="<?php echo e($addNewUrl); ?>" class="btn btn-dark">
            <i class="bi bi-plus-circle me-2"></i><?php echo e($addNewLabel); ?>
        </a>
    </div>
</div>
