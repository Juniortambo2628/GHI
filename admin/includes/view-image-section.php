<?php
/**
 * View Image Section Component
 * Reusable image display section
 * 
 * @param string|null $imageUrl Image URL
 * @param string $altText Alt text for image
 */

if (!isset($imageUrl) || empty($imageUrl)) {
    return;
}

if (!isset($altText)) {
    $altText = 'Image';
}
?>
<div class="mb-4">
    <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($altText); ?>" class="img-fluid rounded">
</div>

