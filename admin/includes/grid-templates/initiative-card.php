<?php
/**
 * Initiative Grid Card Template
 * Used in initiatives list grid view
 * 
 * Available variables: $item (initiative data), $categoryToObjective (mapping array)
 */
$actionsJson = htmlspecialchars(json_encode($item['action_menu'] ?? []), ENT_QUOTES, 'UTF-8');
$categoryToObjective ??= [];
?>
<div class="col-xl-3 col-lg-4 col-md-6" data-item-id="<?php echo $item['id']; ?>">
    <div class="admin-grid-card h-100">
        <?php if (!empty($item['image'])): ?>
            <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>">
        <?php else: ?>
            <div class="bg-light text-center py-5 fw-semibold text-muted">No Image</div>
        <?php endif;
 ?>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="mb-0"><?php echo e($item['title']); ?></h5>
                <button type="button" class="action-menu-trigger" data-action-menu="<?php echo $actionsJson; ?>" aria-label="Open actions">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
            </div>
            <p class="card-meta mb-2"><?php echo e($item['category_label']); ?></p>
            <div class="small mb-3"><?php echo e($item['core_objective']); ?></div>
            <span class="status-chip <?php echo $item['status'] === 'draft' ? 'is-draft' : ''; ?>">
                <?php echo e($item['status_label']); ?>
            </span>
            <?php if (isset($item['created_at'])): ?>
            <p class="card-meta mt-3 mb-0">Created: <?php echo e($item['created_at']); ?></p>
            <?php endif;
 ?>
        </div>
    </div>
</div>
