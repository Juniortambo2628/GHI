    <!-- Navbar start -->
    <div class="container-fluid fixed-top px-0">
        <div class="container px-0">
            <div class="topbar">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-8">
                        <div class="topbar-info d-flex flex-wrap">
                            <a href="mailto:<?php echo e(SITE_EMAIL); ?>" class="text-light me-4"><i class="fas fa-envelope text-white me-2"></i><?php echo e(SITE_EMAIL); ?></a>
                            <a href="tel:<?php echo e(SITE_PHONE_US); ?>" class="text-light"><i class="fas fa-phone-alt text-white me-2"></i><?php echo e(SITE_PHONE_US); ?></a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="topbar-icon d-flex align-items-center justify-content-end">
                            <a href="#" class="btn-square text-white me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn-square text-white me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn-square text-white me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn-square text-white me-2" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="navbar navbar-light bg-light navbar-expand-xl">
                <a href="<?php echo BASE_URL; ?>/index.php" class="navbar-brand ms-3">
                    <img src="<?php echo BASE_URL; ?>/Logo/Square-White-BG.png" alt="<?php echo e(SITE_NAME); ?>" class="navbar-logo-img">
                </a>
                <button class="navbar-toggler py-2 px-3 me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-primary"></span>
                </button>
                <div class="collapse navbar-collapse bg-light" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">
                        <a href="<?php echo BASE_URL; ?>/index.php" class="nav-item nav-link <?php echo isActivePage('home') ? 'active' : ''; ?>">Home</a>
                        <a href="<?php echo BASE_URL; ?>/causes.php" class="nav-item nav-link <?php echo isActivePage('causes') ? 'active' : ''; ?>">Our Causes</a>
                        <a href="<?php echo BASE_URL; ?>/initiatives.php" class="nav-item nav-link <?php echo isActivePage('initiatives') ? 'active' : ''; ?>">Initiatives</a>
                        <a href="<?php echo BASE_URL; ?>/events.php" class="nav-item nav-link <?php echo isActivePage('events') ? 'active' : ''; ?>">Events</a>
                        <a href="<?php echo BASE_URL; ?>/impact.php" class="nav-item nav-link <?php echo isActivePage('impact') ? 'active' : ''; ?>">Our Impact</a>
                        <a href="<?php echo BASE_URL; ?>/stories.php" class="nav-item nav-link <?php echo isActivePage('stories') ? 'active' : ''; ?>">Our Stories</a>
                        <a href="<?php echo BASE_URL; ?>/coming-soon-get-involved.php" class="nav-item nav-link <?php echo isActivePage('get-involved') ? 'active' : ''; ?>">Get Involved</a>
                        <a href="<?php echo BASE_URL; ?>/contact.php" class="nav-item nav-link <?php echo isActivePage('contact') ? 'active' : ''; ?>">Contact</a>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap pt-xl-0 navbar-cta-container">
                        <a href="<?php echo BASE_URL; ?>/donate.php" class="btn-hover-bg btn btn-primary text-white py-2 px-4 me-3">Donate Now</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->
