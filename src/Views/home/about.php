<?php
$missionStatement = site_setting('mission_statement', MISSION);
$visionStatement = site_setting('vision_statement', VISION);
$quoteText = site_setting('quote_banner_text', 'When you lift one, you lift us all.');
$quoteCitation = site_setting('quote_banner_citation', 'East African Proverb');
?>
<!-- About Start -->
<div class="container-fluid about  py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-xl-5">
                <div class="h-100">
                    <img src="<?php echo BASE_URL; ?>/Banners-and-portraits/pexels-magda-ehlers-pexels-2660262.jpg" class="img-fluid w-100 h-100 about-feature-img" alt="Image" loading="eager" width="800" height="600">
                </div>
            </div>
            <div class="col-xl-7">
                <h5 class="text-uppercase text-primary">About Us</h5>
                <h1 class="mb-4"><?php echo e(INTRO_HEADLINE); ?></h1>
                <p class="fs-5 mb-4"><?php echo e(INTRO_TEXT); ?></p>
                <div class="tab-class bg-secondary p-4">
                    <ul class="nav d-flex flex-wrap mb-2">
                        <li class="nav-item mb-3">
                            <a class="d-flex py-2 text-center bg-white active" data-bs-toggle="pill" href="#tab-1">
                                <span class="text-dark about-tab-label">About</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="d-flex py-2 mx-2 mx-md-3 text-center bg-white" data-bs-toggle="pill" href="#tab-2">
                                <span class="text-dark about-tab-label">Mission</span>
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="d-flex py-2 text-center bg-white" data-bs-toggle="pill" href="#tab-3">
                                <span class="text-dark about-tab-label">Vision</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="text-start my-auto">
                                            <h5 class="text-uppercase mb-3">Who We Are</h5>
                                            <p class="mb-4"><?php echo e(WHO_WE_ARE); ?></p>
                                            <div class="d-flex align-items-center justify-content-start">
                                                <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="<?php echo BASE_URL; ?>/about.php">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane fade show p-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="text-start my-auto">
                                            <h5 class="text-uppercase mb-3">Our Mission</h5>
                                            <p class="mb-4"><?php echo e(MISSION); ?></p>
                                            <div class="d-flex align-items-center justify-content-start">
                                                <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="<?php echo BASE_URL; ?>/about.php">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane fade show p-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="text-start my-auto">
                                            <h5 class="text-uppercase mb-3">Our Vision</h5>
                                            <p class="mb-4"><?php echo e(VISION); ?></p>
                                            <div class="d-flex align-items-center justify-content-start">
                                                <a class="btn-hover-bg btn btn-primary text-white py-2 px-4" href="<?php echo BASE_URL; ?>/about.php">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<!-- Quote Banner Start -->
<div class="container-fluid py-5 quote-banner-bg" data-parallax="0.3">
    <div class="container py-5">
        <div class="text-center mx-auto quote-container">
            <blockquote class="blockquote text-white mb-0">
                <p class="fs-2 mb-3 fst-italic">&ldquo;<?php echo e($quoteText); ?>&rdquo;</p>
                <footer class="blockquote-footer text-white-50 mt-3">
                    <cite class="quote-citation"><?php echo e($quoteCitation); ?></cite>
                </footer>
            </blockquote>
        </div>
    </div>
</div>
<!-- Quote Banner End -->


