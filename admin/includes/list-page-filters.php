<?php
/**
 * Admin List Page Filters
 * Reusable component for admin list page filters
 * 
 * Required variables:
 * - $filters: array - Array of filter configurations
 *   Each filter: ['type' => 'text|select', 'name' => 'field_name', 'placeholder' => 'placeholder text', 'value' => current_value, 'options' => array for select]
 * - $showSearch: bool - Whether to show search input (default: true)
 * - $searchValue: string - Current search value
 * - $formId: string - Form ID (default: "filterForm")
 * - $autoSubmit: bool - Whether to auto-submit on change (default: false)
 */

$showSearch ??= true;
$searchValue ??= '';
$formId ??= 'filterForm';
$autoSubmit ??= false;
$filters ??= [];
?>

<!-- Filters and Search -->
<div class="card mb-4 admin-filters">
    <div class="card-body">
        <form method="GET" action="" id="<?php echo e($formId); ?>" <?php echo $autoSubmit ? 'data-auto-submit' : ''; ?>>
            <div class="row g-3">
                <?php if ($showSearch): ?>
                <div class="col-md-<?php echo count($filters) > 0 ? '4' : '12'; ?>">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo e($searchValue); ?>">
                </div>
                <?php endif;
 ?>
                
                <?php foreach ($filters as $filter): ?>
                <div class="col-md-<?php echo $showSearch ? (count($filters) > 2 ? '2' : '4') : (12 / max(1, count($filters))); ?>">
                    <?php if ($filter['type'] === 'select'): ?>
                    <select name="<?php echo e($filter['name']); ?>" class="form-select">
                        <option value=""><?php echo e($filter['placeholder'] ?? 'All'); ?></option>
                        <?php foreach ($filter['options'] as $value => $label): ?>
                        <option value="<?php echo e($value); ?>" <?php echo ($filter['value'] ?? '') === $value ? 'selected' : ''; ?>>
                            <?php echo e($label); ?>
                        </option>
                        <?php endforeach;
 ?>
                    </select>
<?php else: ?>
                    <input type="text" name="<?php echo e($filter['name']); ?>" class="form-control" 
                           placeholder="<?php echo e($filter['placeholder'] ?? ''); ?>" 
                           value="<?php echo e($filter['value'] ?? ''); ?>">
                    <?php endif;
 ?>
                </div>
<?php endforeach;
 ?>
                
                <?php if (!$autoSubmit): ?>
                <div class="col-md-<?php echo $showSearch ? '2' : '12'; ?>">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
                <?php endif;
 ?>
            </div>
        </form>
    </div>
</div>
