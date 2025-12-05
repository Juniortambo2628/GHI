<?php
/**
 * 404 Error Page - Page Not Found
 * Global Harmony Initiative
 */

// Set 404 header
http_response_code(404);

// Page configuration
$pageTitle = '404 - Page Not Found';
$metaDescription = 'The page you are looking for could not be found.';

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section error-hero-bg">
    <div class="container text-center text-white">
        <div class="error-code">
            404
        </div>
        <h1 class="display-3 fw-bold mb-4" data-aos="fade-up">
            Page Not Found
        </h1>
        <p class="lead mb-5 error-lead-text" data-aos="fade-up" data-aos-delay="100">
            Oops! The page you're looking for seems to have wandered off. Don't worry, even the best explorers get lost sometimes.
        </p>
        <div data-aos="fade-up" data-aos-delay="200">
            <a href="<?php echo BASE_URL; ?>" class="btn btn-light btn-lg me-3">
                <i class="bi bi-house-door me-2"></i>
                Go Home
            </a>
            <a href="<?php echo BASE_URL; ?>/about" class="btn btn-outline-light btn-lg">
                <i class="bi bi-info-circle me-2"></i>
                About Us
            </a>
        </div>
    </div>
</section>

<!-- Helpful Links Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="text-center mb-5" data-aos="fade-up">
                    Here's Where You Might Want to Go
                </h2>
                
                <div class="row g-4">
                    <!-- Home -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-purple">
                                    <i class="bi bi-house-door fs-3 text-white"></i>
                                </div>
                                <h5 class="card-title">Home</h5>
                                <p class="card-text text-muted small">
                                    Return to our homepage
                                </p>
                                <a href="<?php echo BASE_URL; ?>" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- About Us -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-pink">
                                    <i class="bi bi-people fs-3 text-white"></i>
                                </div>
                                <h5 class="card-title">About Us</h5>
                                <p class="card-text text-muted small">
                                    Learn about our mission
                                </p>
                                <a href="<?php echo BASE_URL; ?>/about" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Our Work -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-blue">
                                    <i class="bi bi-briefcase fs-3 text-white"></i>
                                </div>
                                <h5 class="card-title">Our Work</h5>
                                <p class="card-text text-muted small">
                                    Explore our initiatives
                                </p>
                                <a href="<?php echo BASE_URL; ?>/our-work" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="card h-100 text-center border-0 shadow-sm hover-lift">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-green">
                                    <i class="bi bi-envelope fs-3 text-white"></i>
                                </div>
                                <h5 class="card-title">Contact</h5>
                                <p class="card-text text-muted small">
                                    Get in touch with us
                                </p>
                                <a href="<?php echo BASE_URL; ?>/contact" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <h3 class="mb-4">Looking for Something Specific?</h3>
                        <p class="text-muted mb-4">
                            Try searching for what you need
                        </p>
                        <form action="<?php echo BASE_URL; ?>/search" method="GET" class="d-flex gap-2">
                            <input 
                                type="text" 
                                name="q" 
                                class="form-control form-control-lg" 
                                placeholder="Search our website..." 
                                required
                            >
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.error-page-hero {
    position: relative;
    overflow: hidden;
}

.error-page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
    opacity: 0.3;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

