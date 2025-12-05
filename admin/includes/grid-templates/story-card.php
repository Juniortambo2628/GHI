<?php
/**
 * Story Grid Card Template
 * Used in stories list grid view
 * 
 * Available variables: $item (story data)
 */
$actionsJson = htmlspecialchars(json_encode($item['action_menu'] ?? []), ENT_QUOTES, 'UTF-8');
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
            <p class="card-meta mb-2">By: <?php echo e($item['author']); ?></p>
            <p class="card-meta mb-2">
                <?php echo e($item['category_label']); ?> • <?php echo e($item['region']); ?>
            </p>
            <div class="d-flex gap-3 mb-3">
                <small><i class="bi bi-heart me-1"></i><?php echo e($item['likes']); ?></small>
                <small><i class="bi bi-chat me-1"></i><?php echo e($item['comments']); ?></small>
                <small><i class="bi bi-calendar me-1"></i><?php echo e($item['date_formatted']); ?></small>
            </div>
            <span class="status-chip <?php echo $item['status'] === 'draft' ? 'is-draft' : ''; ?>">
                <?php echo e($item['status_label']); ?>
            </span>
        </div>
    </div>
</div>
