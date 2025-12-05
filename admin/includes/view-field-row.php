<?php
/**
 * View Field Row Component
 * Reusable field display row
 * 
 * @param string $label Field label
 * @param mixed $value Field value
 * @param bool $isLink Whether the value should be a link
 * @param string|null $linkUrl URL if isLink is true
 * @param bool $allowHtml Whether to allow HTML in value (default: false)
 */

if (!isset($label)) {
    $label = 'Field';
}

if (!isset($value)) {
    $value = 'N/A';
}

if (!isset($isLink)) {
    $isLink = false;
}

if (!isset($linkUrl)) {
    $linkUrl = null;
}

if (!isset($allowHtml)) {
    $allowHtml = false;
}
?>
<div class="row mb-3">
    <div class="col-sm-3 fw-bold"><?php echo e($label); ?>:</div>
    <div class="col-sm-9">
        <?php if ($isLink && $linkUrl): ?>
            <a href="<?php echo e($linkUrl); ?>"><?php echo e($value); ?></a>
        <?php elseif ($allowHtml): ?>
            <?php echo $value; ?>
        <?php else: ?>
            <?php echo is_string($value) ? e($value) : $value; ?>
        <?php endif;
 ?>
    </div>
</div>

