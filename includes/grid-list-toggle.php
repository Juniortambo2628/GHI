<?php
/**
 * Grid/List Toggle Component
 *
 * @param string $viewMode - Current view mode ('grid' or 'list')
 * @param string $storageKey - LocalStorage key for persisting view preference
 */

$viewMode ??= 'grid';
$storageKey ??= 'viewMode';
?>

<div class="view-toggle" data-storage-key="<?php echo e($storageKey); ?>" data-default-view="<?php echo e($viewMode); ?>">
    <div class="btn-group" role="group" aria-label="View Toggle">
        <input type="radio" class="btn-check" name="viewMode" id="viewGrid" value="grid" <?php echo $viewMode === 'grid' ? 'checked' : ''; ?>>
        <label class="btn btn-outline-primary" for="viewGrid" title="Grid View">
            <i class="fas fa-th"></i>
        </label>
        
        <input type="radio" class="btn-check" name="viewMode" id="viewList" value="list" <?php echo $viewMode === 'list' ? 'checked' : ''; ?>>
        <label class="btn btn-outline-primary" for="viewList" title="List View">
            <i class="fas fa-list"></i>
        </label>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/js/view-toggle.js" defer></script>
