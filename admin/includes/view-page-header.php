<?php
/**
 * View Page Header Component
 * Reusable header for all view pages
 * 
 * @param string $pageTitle Page title
 * @param array $breadcrumbs Breadcrumb items [['label' => '...', 'url' => '...'], ...]
 * @param string $backUrl URL to go back to list page
 * @param string $editUrl URL to edit page
 * @param int|null $entityId Entity ID for edit link
 */

if (!isset($pageTitle)) {
    $pageTitle = 'View Item';
}

if (!isset($breadcrumbs)) {
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
        ['label' => 'View', 'url' => ''],
    ];
}

if (!isset($backUrl)) {
    $backUrl = BASE_URL . '/admin/index.php';
}

if (!isset($editUrl)) {
    $editUrl = '';
}

if (!isset($entityId)) {
    $entityId = null;
}
?>
<div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><?php echo e($pageTitle); ?></h4>
    <div>
        <a href="<?php echo e($backUrl); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <?php if ($editUrl && $entityId): ?>
            <a href="<?php echo e($editUrl); ?>?id=<?php echo (int)$entityId; ?>" class="btn btn-primary ms-2">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        <?php endif;
 ?>
    </div>
</div>

