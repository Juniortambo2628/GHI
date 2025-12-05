<?php
/**
 * View Page Sidebar Component
 * Reusable sidebar for status and actions
 * 
 * @param string $status Status value (e.g., 'active', 'published', 'draft')
 * @param string $editUrl URL to edit page
 * @param string $deleteUrl URL to delete page
 * @param string $entityName Entity name for delete confirmation (e.g., 'cause', 'event')
 * @param int|null $entityId Entity ID
 */

if (!isset($status)) {
    $status = 'active';
}

if (!isset($editUrl)) {
    $editUrl = '';
}

if (!isset($deleteUrl)) {
    $deleteUrl = '';
}

if (!isset($entityName)) {
    $entityName = 'item';
}

if (!isset($entityId)) {
    $entityId = null;
}

// Determine status badge color
$statusColor = 'secondary';
if ($status === 'active' || $status === 'published') {
    $statusColor = 'success';
} elseif ($status === 'draft' || $status === 'inactive') {
    $statusColor = 'secondary';
}
?>
<div class="col-md-4">
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Status</h6>
        </div>
        <div class="card-body">
            <span class="badge bg-<?php echo e($statusColor); ?> fs-6">
                <?php echo ucfirst((string) $status); ?>
            </span>
        </div>
    </div>
    
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0">Actions</h6>
        </div>
        <div class="card-body">
            <?php if ($editUrl && $entityId): ?>
                <a href="<?php echo e($editUrl); ?>?id=<?php echo (int)$entityId; ?>" class="btn btn-primary btn-sm w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i>Edit <?php echo ucfirst(e($entityName)); ?>
                </a>
            <?php endif;
 ?>
            
            <?php if ($deleteUrl && $entityId): ?>
                <a href="<?php echo e($deleteUrl); ?>?id=<?php echo (int)$entityId; ?>" class="btn btn-danger btn-sm w-100 mb-2" data-delete-confirm="Are you sure you want to delete this <?php echo e($entityName); ?>?">
                    <i class="bi bi-trash me-1"></i>Delete
                </a>
            <?php endif;
 ?>
            
            <button class="btn btn-outline-secondary btn-sm w-100 print-trigger">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>
</div>

