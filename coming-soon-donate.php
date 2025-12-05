<?php
/**
 * Coming Soon - Donate Now Page
 * Global Harmony Initiative
 */

// Page configuration
$pageTitle = 'Donate Now - Coming Soon';
$metaDescription = 'Our donation page is coming soon. Join our mailing list to be notified when you can support our cause.';

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section coming-soon-hero-bg">
    <div class="container text-center text-white position-relative hero-content-layer">
        <div class="coming-soon-icon mb-4" data-aos="zoom-in">
            <i class="bi bi-heart-fill coming-soon-icon-large"></i>
        </div>
        <h1 class="display-2 fw-bold mb-4" data-aos="fade-up">
            Donate Now
        </h1>
        <p class="lead mb-2 coming-soon-subtitle" data-aos="fade-up" data-aos-delay="100">
            Coming Soon
        </p>
        <p class="lead mb-5 coming-soon-description" data-aos="fade-up" data-aos-delay="200">
            We're building a secure and transparent donation system that will make it easy for you to support our mission of creating global harmony.
        </p>
    </div>
    
    <!-- Animated background elements -->
    <div class="animated-bg">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>
</section>

<!-- What's Coming Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="text-center mb-5" data-aos="fade-up">
                    What to Expect
                </h2>
                
                <div class="row g-4">
                    <!-- Secure Payments -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg mx-auto mb-3 icon-circle-gradient-purple">
                                    <i class="bi bi-shield-check fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Secure Payments</h5>
                                <p class="card-text text-muted small">
                                    Multiple payment options with bank-level security encryption
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Transparent Tracking -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg mx-auto mb-3 icon-circle-gradient-pink">
                                    <i class="bi bi-graph-up fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Impact Tracking</h5>
                                <p class="card-text text-muted small">
                                    See exactly how your donation makes a difference
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recurring Donations -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg mx-auto mb-3 icon-circle-gradient-blue">
                                    <i class="bi bi-arrow-repeat fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Recurring Options</h5>
                                <p class="card-text text-muted small">
                                    Set up monthly donations for sustained impact
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tax Receipts -->
                    <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg mx-auto mb-3 icon-circle-gradient-green">
                                    <i class="bi bi-file-text fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Tax Receipts</h5>
                                <p class="card-text text-muted small">
                                    Instant tax-deductible receipts for your records
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Other Ways to Help -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h2 class="mb-4">Want to Help Right Now?</h2>
                <p class="lead text-muted mb-5">
                    While our donation page is being built, here are other ways you can support our mission:
                </p>
                
                <div class="row g-4">
                    <!-- Volunteer -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-people fs-1 text-primary mb-3 d-block"></i>
                                <h5>Volunteer</h5>
                                <p class="text-muted small mb-3">
                                    Join our team of dedicated volunteers
                                </p>
                                <a href="<?php echo BASE_URL; ?>/coming-soon-get-involved" class="btn btn-outline-primary btn-sm">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Share -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-share fs-1 text-success mb-3 d-block"></i>
                                <h5>Share Our Mission</h5>
                                <p class="text-muted small mb-3">
                                    Spread the word on social media
                                </p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="https://facebook.com/sharer.php?u=<?php echo urlencode(SITE_URL); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL); ?>&text=<?php echo urlencode('Support Global Harmony Initiative'); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-envelope fs-1 text-info mb-3 d-block"></i>
                                <h5>Get in Touch</h5>
                                <p class="text-muted small mb-3">
                                    Have questions? Contact us directly
                                </p>
                                <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-outline-primary btn-sm">
                                    Contact Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="fade-up">
                <h2 class="mb-4">Be the First to Know</h2>
                <p class="lead mb-5">
                    Sign up for our newsletter and we'll notify you as soon as our donation page goes live!
                </p>
                <form action="<?php echo BASE_URL; ?>/api/newsletter-subscribe" method="POST" class="row g-3 justify-content-center">
                    <div class="col-md-6">
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control form-control-lg" 
                            placeholder="Enter your email" 
                            required
                        >
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-light btn-lg px-5">
                            Notify Me
                        </button>
                    </div>
                </form>
                <p class="small mt-3 opacity-75">
                    We respect your privacy. Unsubscribe anytime.
                </p>
            </div>
        </div>
    </div>
</section>

<style>
.coming-soon-hero {
    min-height: 70vh;
    display: flex;
    align-items: center;
}

.animated-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.animated-bg .circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 20s infinite ease-in-out;
}

.animated-bg .circle:nth-child(1) {
    width: 300px;
    height: 300px;
    top: -150px;
    left: -150px;
    animation-delay: 0s;
}

.animated-bg .circle:nth-child(2) {
    width: 200px;
    height: 200px;
    top: 50%;
    right: -100px;
    animation-delay: 5s;
}

.animated-bg .circle:nth-child(3) {
    width: 400px;
    height: 400px;
    bottom: -200px;
    left: 50%;
    animation-delay: 10s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-50px) rotate(180deg);
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

