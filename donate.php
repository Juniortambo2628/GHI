<?php
/**
 * Donate - Coming Soon
 * Global Harmony Initiative
 */

require_once __DIR__ . '/config/config.php';

$pageTitle = 'Donate - Coming Soon';
$pageDescription = 'Support our mission. Donation page coming soon.';

require_once __DIR__ . '/src/Views/partials/head.php';
require_once __DIR__ . '/src/Views/partials/navbar.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <i class="bi bi-hourglass-split" style="font-size: 5rem; color: var(--gold);"></i>
            </div>
            <h1 class="display-4 mb-4">Donation Page Coming Soon</h1>
            <p class="lead text-muted mb-4">
                We're working with our payment partners to bring you a secure and seamless donation experience.
            </p>
            <p class="text-muted mb-5">
                In the meantime, you can still support our mission by volunteering or sharing our cause with others.
            </p>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-dark btn-lg">
                    <i class="bi bi-house me-2"></i>Return Home
                </a>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-outline-dark btn-lg">
                    <i class="bi bi-envelope me-2"></i>Contact Us
                </a>
            </div>
            
            <div class="mt-5 pt-5">
                <h4 class="mb-4">Other Ways to Support</h4>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill text-primary" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">Volunteer</h5>
                                <p class="text-muted small">Join our team and make a direct impact</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-share-fill text-success" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3">Share</h5>
                                <p class="text-muted small">Spread awareness about our cause</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="bi bi-envelope-heart-fill" style="font-size: 2.5rem; color: var(--gold);"></i>
                                <h5 class="mt-3">Stay Updated</h5>
                                <p class="text-muted small">Subscribe to our newsletter</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/src/Views/partials/footer.php';
