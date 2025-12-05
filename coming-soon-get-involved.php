<?php
/**
 * Coming Soon - Get Involved / Join Us Page
 * Global Harmony Initiative
 */

// Page configuration
$pageTitle = 'Get Involved - Coming Soon';
$metaDescription = 'Join our community of changemakers. Volunteer opportunities coming soon.';

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section coming-soon-hero">
    <div class="container text-center text-white position-relative hero-content-layer">
        <div class="coming-soon-icon mb-4" data-aos="zoom-in">
            <i class="bi bi-people-fill coming-soon-icon-large"></i>
        </div>
        <h1 class="display-2 fw-bold mb-4" data-aos="fade-up">
            Get Involved
        </h1>
        <p class="lead mb-2 coming-soon-subtitle" data-aos="fade-up" data-aos-delay="100">
            Coming Soon
        </p>
        <p class="lead mb-5 coming-soon-description" data-aos="fade-up" data-aos-delay="200">
            We're creating an exciting platform where you can join our community, volunteer for causes you care about, and make a real difference in the world.
        </p>
    </div>
    
    <!-- Animated background elements -->
    <div class="animated-bg">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>
</section>

<!-- Ways to Get Involved Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="text-center mb-5" data-aos="fade-up">
                    How You Can Make a Difference
                </h2>
                
                <div class="row g-4">
                    <!-- Volunteer -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg icon-circle-gradient-purple mx-auto mb-3">
                                    <i class="bi bi-hand-thumbs-up fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Volunteer</h5>
                                <p class="card-text text-muted small">
                                    Join our team of dedicated volunteers working on projects that matter
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mentor -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg icon-circle-gradient-pink mx-auto mb-3">
                                    <i class="bi bi-person-badge fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Become a Mentor</h5>
                                <p class="card-text text-muted small">
                                    Share your skills and experience to empower others in our community
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Organize Events -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg icon-circle-gradient-blue mx-auto mb-3">
                                    <i class="bi bi-calendar-event fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Organize Events</h5>
                                <p class="card-text text-muted small">
                                    Help plan and execute community events and initiatives
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fundraise -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg icon-circle-gradient-green mx-auto mb-3">
                                    <i class="bi bi-piggy-bank fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Fundraise</h5>
                                <p class="card-text text-muted small">
                                    Start your own fundraising campaign for causes you're passionate about
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Corporate Partnership -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg icon-circle-gradient-sunrise mx-auto mb-3">
                                    <i class="bi bi-building fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Corporate Partnership</h5>
                                <p class="card-text text-muted small">
                                    Partner with us to make a bigger impact through your organization
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Join Our Team -->
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                        <div class="card h-100 text-center border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-circle icon-circle-lg icon-circle-gradient-deepsea mx-auto mb-3">
                                    <i class="bi bi-briefcase fs-2 text-white"></i>
                                </div>
                                <h5 class="card-title mb-3">Join Our Team</h5>
                                <p class="card-text text-muted small">
                                    Explore career opportunities and work full-time on our mission
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <h2 class="mb-4">Why Join Our Community?</h2>
                <div class="mb-4">
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>Make Real Impact</h5>
                            <p class="text-muted">
                                See the direct results of your efforts in communities around the world
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>Develop New Skills</h5>
                            <p class="text-muted">
                                Learn from experienced professionals and expand your capabilities
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>Build Connections</h5>
                            <p class="text-muted">
                                Network with like-minded individuals from diverse backgrounds
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5>Flexible Commitment</h5>
                            <p class="text-muted">
                                Choose opportunities that fit your schedule and interests
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <h3 class="mb-4">Interested in Learning More?</h3>
                        <p class="text-muted mb-4">
                            Leave your information and we'll reach out when our volunteer platform launches.
                        </p>
                        <form action="<?php echo BASE_URL; ?>/api/volunteer-interest" method="POST">
                            <div class="mb-3">
                                <input 
                                    type="text" 
                                    name="name" 
                                    class="form-control" 
                                    placeholder="Your Name" 
                                    required
                                >
                            </div>
                            <div class="mb-3">
                                <input 
                                    type="email" 
                                    name="email" 
                                    class="form-control" 
                                    placeholder="Your Email" 
                                    required
                                >
                            </div>
                            <div class="mb-3">
                                <select name="interest" class="form-select" required>
                                    <option value="">I'm interested in...</option>
                                    <option value="volunteer">General Volunteering</option>
                                    <option value="mentor">Mentorship</option>
                                    <option value="events">Event Organization</option>
                                    <option value="fundraising">Fundraising</option>
                                    <option value="corporate">Corporate Partnership</option>
                                    <option value="career">Career Opportunities</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea 
                                    name="message" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Tell us a bit about yourself (optional)"
                                ></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-envelope me-2"></i>
                                Keep Me Updated
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Current Opportunities Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <h2 class="mb-4">Can't Wait to Get Started?</h2>
                <p class="lead mb-5">
                    While we're building our volunteer platform, you can still connect with us directly!
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="<?php echo BASE_URL; ?>/contact" class="btn btn-light btn-lg">
                        <i class="bi bi-envelope me-2"></i>
                        Contact Us
                    </a>
                    <a href="<?php echo BASE_URL; ?>/about" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-info-circle me-2"></i>
                        Learn More About Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

