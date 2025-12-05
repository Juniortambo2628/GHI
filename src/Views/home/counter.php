<!-- Counter Start -->
<div class="container-fluid counter py-5 counter-section-bg">
    <div class="container">
        <div class="text-center mx-auto pb-5 section-header-container">
            <p class="mb-4 text-white">Through our collective efforts, we've made significant progress in empowering communities across East Africa. Every number represents lives touched and futures transformed.</p>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="counter-item text-center p-5">
                    <i class="fas fa-project-diagram fa-4x text-white"></i>
                    <h3 class="text-white my-4">Initiatives</h3>
                    <div class="counter-counting">
                        <span class="text-white fs-2 fw-bold"><?php echo number_format((int) $pageData['counters']['initiatives']); ?></span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="counter-item text-center p-5">
                    <i class="fas fa-calendar-check fa-4x text-white"></i>
                    <h3 class="text-white my-4">Activities</h3>
                    <div class="counter-counting text-center w-100 counter-display">
                        <span class="text-white fs-2 fw-bold"><?php echo number_format((int) $pageData['counters']['events']); ?></span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="counter-item text-center p-5">
                    <i class="fas fa-map-marked-alt fa-4x text-white"></i>
                    <h3 class="text-white my-4">Communities</h3>
                    <div class="counter-counting text-center w-100 counter-display">
                        <span class="text-white fs-2 fw-bold"><?php echo number_format((int) $pageData['counters']['communities']); ?></span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="counter-item text-center p-5">
                    <i class="fas fa-heart fa-4x text-white"></i>
                    <h3 class="text-white my-4">Lives Changed</h3>
                    <div class="counter-counting text-center w-100 counter-display">
                        <span class="text-white fs-2 fw-bold"><?php echo number_format((int) $pageData['counters']['lives_impacted']); ?></span>
                        <span class="h1 fw-bold text-white">+</span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-center">
                    <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="<?php echo BASE_URL; ?>/get-involved.php">Join With Us</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Counter End -->


