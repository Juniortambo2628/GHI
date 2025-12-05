<?php
/**
 * Donation Success Page
 * Shown after successful Stripe payment
 */

require_once __DIR__ . '/config/config.php';

use GHI\Models\Donation;

$sessionId = $_GET['session_id'] ?? '';
$donationId = $_GET['donation_id'] ?? 0;

$donation = null;
$stripeSession = null;

if ($donationId) {
    $donationModel = new Donation();
    $donation = $donationModel->find($donationId);
    
    // Verify Stripe payment if configured
    $stripeSecretKey = getenv('STRIPE_SECRET_KEY');
    if (!empty($stripeSecretKey) && $sessionId) {
        try {
            \Stripe\Stripe::setApiKey($stripeSecretKey);
            $stripeSession = \Stripe\Checkout\Session::retrieve($sessionId);
            
            // Update donation status if payment succeeded
            if ($stripeSession->payment_status === 'paid' && $donation['status'] !== 'completed') {
                $donationModel->updateStatus($donationId, 'completed');
                $donation['status'] = 'completed';
            }
        } catch (Exception $e) {
            // Log error but still show success page
            error_log('Stripe verification error: ' . $e->getMessage());
        }
    }
}

// Set page metadata
$pageTitle = 'Thank You - ' . SITE_NAME;
$pageDescription = 'Thank you for your generous donation to Global Harmony Initiative.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5">
    <div class="container text-center py-5">
        <h1 class="display-2 text-white mb-4 animated slideInDown">Thank You!</h1>
    </div>
</div>
<!-- Page Header End -->

<!-- Success Message Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="bg-light p-5 rounded">
                    <i class="fa fa-check-circle text-success mb-4" style="font-size: 80px;"></i>
                    <h2 class="mb-4">Your Donation Was Successful!</h2>
                    
                    <?php if ($donation): ?>
                    <div class="bg-white p-4 rounded mb-4">
                        <h5 class="mb-3">Donation Details</h5>
                        <div class="row">
                            <div class="col-md-6 text-start">
                                <p><strong>Donation ID:</strong></p>
                                <p><strong>Amount:</strong></p>
                                <p><strong>Type:</strong></p>
                                <p><strong>Status:</strong></p>
                            </div>
                            <div class="col-md-6 text-start">
                                <p>#<?php echo $donation['id']; ?></p>
                                <p>$<?php echo number_format($donation['amount'], 2); ?></p>
                                <p><?php echo ucfirst(str_replace('_', ' ', $donation['donation_type'])); ?></p>
                                <p><span class="badge bg-success"><?php echo ucfirst($donation['status']); ?></span></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <p class="mb-4">Thank you for your generous donation to Global Harmony Initiative. Your support helps us create positive change in East Africa.</p>
                    <p class="mb-4">A confirmation email has been sent to <strong><?php echo e($donation['email'] ?? ''); ?></strong></p>
                    
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary py-3 px-5">Return Home</a>
                        <a href="<?php echo BASE_URL; ?>/impact.php" class="btn btn-secondary py-3 px-5">View Our Impact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Success Message End -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
