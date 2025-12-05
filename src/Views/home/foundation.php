<!-- Our Foundation Start -->
<div class="container-fluid py-5 foundation-section-bg">
    <div class="container py-5">
        <div class="text-center mx-auto mb-5 section-header-container">
            <h5 class="text-uppercase text-primary">About Us</h5>
            <h1 class="mb-0">Our Foundation</h1>
        </div>
        <div class="row g-4">
            <!-- Left Column: Mission & Vision -->
<?php
$missionStatement = site_setting('mission_statement', MISSION);
$visionStatement = site_setting('vision_statement', VISION);
?>
            <div class="col-lg-5">
                <div class="foundation-card rounded p-4 mb-4 foundation-card-gradient">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="text-white mb-0">Our Mission</h5>
                        <i class="bi bi-bullseye text-white foundation-icon"></i>
                    </div>
                    <p class="mb-0 text-white"><?php echo e($missionStatement); ?></p>
                </div>
                <div class="foundation-card rounded p-4 foundation-card-gradient">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="text-white mb-0">Our Vision</h5>
                        <i class="bi bi-eye text-white foundation-icon"></i>
                    </div>
                    <p class="mb-0 text-white"><?php echo e($visionStatement); ?></p>
                </div>
            </div>
            <!-- Right Column: Our Values -->
            <div class="col-lg-7">
                <div class="foundation-card bg-white rounded p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="text-primary mb-0">Our Values</h5>
                        <i class="bi bi-heart text-primary foundation-icon"></i>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($pageData['coreValues'] as $value) : ?>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-<?php echo e($value['icon']); ?> text-primary me-2"></i>
                                <strong class="text-primary small"><?php echo e($value['name']); ?></strong>
                            </div>
                            <p class="small mb-0 value-description"><?php echo e($value['description']); ?></p>
                        </div>
                        <?php endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Our Foundation End -->


