<?php

/**
 * Admin Hero Area Component
 * Global Harmony Initiative Admin Dashboard
 */

$pageTitle ??= 'Admin Dashboard';
$breadcrumbs ??= [['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php']];
?>
<div class="admin-hero">
    <div class="container-fluid">
        <h1 class="admin-page-title"><?php echo e($pageTitle); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $index => $crumb) : ?>
                    <?php if ($index === count($breadcrumbs) - 1) : ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e($crumb['label']); ?></li>
                    <?php else : ?>
                        <li class="breadcrumb-item">
                            <a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['label']); ?></a>
                        </li>
                    <?php endif;
                    ?>
                <?php endforeach;
                ?>
            </ol>
        </nav>
    </div>
</div>

