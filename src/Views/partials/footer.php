    <!-- Footer Start -->
    <div class="container-fluid footer py-5 footer-bg">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item">
                        <h4 class="mb-4 text-primary">About Us</h4>
                        <p class="mb-4 footer-text"><?php echo e(SITE_TAGLINE); ?>. We are a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development.</p>
                        <h4 class="mb-4 text-primary">Newsletter</h4>
                        <div class="position-relative mx-auto">
                            <form id="newsletter-form" class="d-flex">
                                <?php echo csrf_field(); ?>
                                <input class="form-control border-0 bg-secondary w-100 py-3 ps-4 pe-5" type="email" name="email" placeholder="Enter your email" required>
                                <button type="submit" class="btn-hover-bg btn btn-primary position-absolute top-0 end-0 py-2 mt-2 me-2 text-white">SignUp</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="mb-4 text-primary">Quick Links</h4>
                        <a href="<?php echo BASE_URL; ?>/about"><i class="fas fa-angle-right me-2"></i> About Us</a>
                        <a href="<?php echo BASE_URL; ?>/causes"><i class="fas fa-angle-right me-2"></i> Our Causes</a>
                        <a href="<?php echo BASE_URL; ?>/initiatives"><i class="fas fa-angle-right me-2"></i> Our Initiatives</a>
                        <a href="<?php echo BASE_URL; ?>/events"><i class="fas fa-angle-right me-2"></i> Events & Activities</a>
                        <a href="<?php echo BASE_URL; ?>/impact"><i class="fas fa-angle-right me-2"></i> Our Impact</a>
                        <a href="<?php echo BASE_URL; ?>/stories"><i class="fas fa-angle-right me-2"></i> Our Stories</a>
                        <a href="<?php echo BASE_URL; ?>/coming-soon-get-involved"><i class="fas fa-angle-right me-2"></i> Get Involved</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                        <h4 class="mb-4 text-primary">Contact Us</h4>
                        <p class="mb-2 footer-text"><i class="fas fa-envelope text-primary me-2"></i><?php echo e(SITE_EMAIL); ?></p>
                        <p class="mb-2 footer-text"><i class="fas fa-phone-alt text-primary me-2"></i>US: <?php echo e(SITE_PHONE_US); ?></p>
                        <p class="mb-2 footer-text"><i class="fas fa-phone-alt text-primary me-2"></i>EA: <?php echo e(SITE_PHONE_EA); ?></p>
                        <div class="d-flex align-items-center mt-3">
                            <a href="#" class="btn-hover-color btn-square text-primary me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn-hover-color btn-square text-primary me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn-hover-color btn-square text-primary me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn-hover-color btn-square text-primary me-0" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item">
                        <h4 class="mb-4 text-primary">Our Mission</h4>
                        <p class="mb-4 footer-text">To bridge global compassion with local action, empowering communities in East Africa through sustainable programs in education, healthcare, and economic development.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright py-4">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-md-0">
                    <span class="text-body"><a href="#"><i class="fas fa-copyright text-light me-2"></i><?php echo date('Y'); ?> <?php echo e(SITE_NAME); ?></a>, All rights reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end text-body">
                    <p class="mb-0">A U.S. Registered 501(c)(3) Nonprofit Organization</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top"><i class="fa fa-arrow-up"></i></a>   

    <!-- JavaScript Libraries -->
    <!-- Critical: jQuery and Bootstrap loaded normally (needed for initial render) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/lib/bootstrap/bootstrap.bundle.min.js"></script>
    
    <!-- Non-critical: Defer loading of animation and enhancement libraries -->
    <script src="<?php echo BASE_URL; ?>/lib/easing/easing.min.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/lib/waypoints/waypoints.min.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/lib/counterup/counterup.min.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/lib/owlcarousel/owl.carousel.min.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/lib/lightbox/js/lightbox.min.js" defer></script>
    
    <!-- Template Javascript -->
    <script src="<?php echo BASE_URL; ?>/js/main.js" defer></script>
    
    
    <!-- Modern JavaScript with all packages - Load immediately to initialize lazy load and AOS -->
    <script type="module" src="<?php echo BASE_URL; ?>/dist/js/modern.js"></script>

</body>

</html>
