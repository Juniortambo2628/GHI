<?php
/**
 * Contact Us Page
 * Global Harmony Initiative Website
 */

require_once __DIR__ . '/config/config.php';

// Set page metadata
$pageTitle = 'Contact Us - ' . SITE_NAME;
$pageDescription = 'Get in touch with Global Harmony Initiative. We would love to hear from you.';

// Include header
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5">
    <div class="container text-center py-5">
        <h1 class="display-2 text-white mb-4 animated slideInDown">Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0 animated slideInDown">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item text-white" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header End -->

<!-- Contact Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h5 class="text-uppercase text-primary">Get In Touch</h5>
                <h1 class="mb-4">Contact For Any Query</h1>
                <p class="mb-4">Have a question or want to get involved? We'd love to hear from you. Fill out the form and we'll get back to you as soon as possible.</p>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 10px;">
                        <i class="fa fa-map-marker-alt text-white"></i>
                    </div>
                    <div class="ps-3">
                        <h5>Office Address</h5>
                        <span><?php echo site_setting('contact_address', 'Nairobi, Kenya'); ?></span>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 10px;">
                        <i class="fa fa-envelope text-white"></i>
                    </div>
                    <div class="ps-3">
                        <h5>Email Us</h5>
                        <span><?php echo site_setting('contact_email', 'info@globalharmony.org'); ?></span>
                    </div>
                </div>
                
                <div class="d-flex mb-0">
                    <div class="bg-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 10px;">
                        <i class="fa fa-phone-alt text-white"></i>
                    </div>
                    <div class="ps-3">
                        <h5>Call Us</h5>
                        <span><?php echo site_setting('contact_phone', '+254 XXX XXXXXX'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="bg-light p-5 rounded">
                    <form id="contact-form" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" required>
                                    <label for="firstname">First Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" required>
                                    <label for="lastname">Last Name</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                                    <label for="subject">Subject</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Leave a message here" id="message" name="message" style="height: 160px" required></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Contact End -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
