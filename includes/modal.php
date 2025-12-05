<?php
/**
 * Reusable Modal Component for Listing Details
 *
 * @param string $modalId - Unique ID for the modal
 * @param string $title - Modal title
 * @param array $data - Data to display in modal
 */

$modalId ??= 'detailModal';
$title ??= 'Details';
$data ??= [];
?>

<!-- Modal -->
<div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" aria-labelledby="<?php echo e($modalId); ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-with-image">
                <div class="modal-header-overlay"></div>
                <div class="modal-header-content">
                    <h5 class="modal-title modal-header-title" id="<?php echo e($modalId); ?>Label"><?php echo e($title); ?></h5>
                </div>
                <button type="button" class="btn-close btn-close-white modal-header-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <?php if (! empty($data['subtitle'])): ?>
                    <p class="text-muted mb-3"><?php echo e($data['subtitle']); ?></p>
                <?php endif;
 ?>
                
                <?php if (! empty($data['description'])): ?>
                    <div class="mb-3">
                        <?php echo $data['description']; ?>
                    </div>
                <?php endif;
 ?>
                
                <?php if (! empty($data['meta'])): ?>
                    <div class="row mb-3">
                        <?php foreach ($data['meta'] as $key => $value): ?>
                            <div class="col-md-6 mb-2">
                                <strong><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                                <span><?php echo e($value); ?></span>
                            </div>
                        <?php endforeach;
 ?>
                    </div>
<?php endif;
 ?>
                
                <?php if (! empty($data['tags'])): ?>
                    <div class="mb-3">
                        <?php foreach ($data['tags'] as $tag): ?>
                            <span class="badge bg-primary me-1"><?php echo e($tag); ?></span>
                        <?php endforeach;
 ?>
                    </div>
<?php endif;
 ?>
            </div>
            <div class="modal-footer">
                <?php if (! empty($data['action_url'])): ?>
                    <a href="<?php echo e($data['action_url']); ?>" class="btn btn-primary"><?php echo e($data['action_text'] ?? 'Learn More'); ?></a>
                <?php endif;
 ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal script is loaded via js/modals.js -->

