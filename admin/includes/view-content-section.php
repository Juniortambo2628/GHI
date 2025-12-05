<?php
/**
 * View Content Section Component
 * Reusable content display section
 * 
 * @param string $title Section title
 * @param string $content Content to display (HTML allowed)
 * @param bool $showIfEmpty Whether to show section if content is empty
 */

if (!isset($title)) {
    $title = 'Content';
}

if (!isset($content)) {
    $content = '';
}

if (!isset($showIfEmpty)) {
    $showIfEmpty = false;
}

if (empty($content) && !$showIfEmpty) {
    return;
}
?>
<div class="mb-4">
    <h6 class="mb-2"><?php echo e($title); ?></h6>
    <div class="card bg-light">
        <div class="card-body">
            <?php echo $content ?: 'No content available.'; ?>
        </div>
    </div>
</div>

