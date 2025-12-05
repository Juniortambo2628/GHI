<?php
/**
 * Contact Submission View Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Models\ContactSubmission;
use GHI\Services\CsrfService;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Check authentication
require_login();

$contactModel = new ContactSubmission();
$contact = null;
$errors = [];
$success = false;

// Get contact ID
$contactId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load contact
if ($contactId > 0) {
    $contact = $contactModel->find($contactId);
    if ($contact === null || $contact === []) {
        header('Location: ' . BASE_URL . '/admin/contact-submissions.php');
        exit;
    }
    
    // Mark as read if status is new
    if ($contact['status'] === 'new') {
        $contactModel->update($contactId, ['status' => 'read']);
        $contact['status'] = 'read';
    }
} else {
    header('Location: ' . BASE_URL . '/admin/contact-submissions.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Validate CSRF token
    $token = $_POST[CSRF_TOKEN_NAME] ?? $_POST['_token'] ?? '';
    if (!csrf_validate($token)) {
        $errors['general'] = 'Invalid security token. Please refresh the page and try again.';
    } else {
        $action = $_POST['action'];
        
        if ($action === 'mark_replied') {
            $contactModel->update($contactId, ['status' => 'replied']);
            $contact['status'] = 'replied';
            $success = 'Contact marked as replied!';
            log_message('info', 'Contact marked as replied', ['contact_id' => $contactId]);
        } elseif ($action === 'mark_read') {
            $contactModel->update($contactId, ['status' => 'read']);
            $contact['status'] = 'read';
            $success = 'Contact marked as read!';
            log_message('info', 'Contact marked as read', ['contact_id' => $contactId]);
        } elseif ($action === 'mark_new') {
            $contactModel->update($contactId, ['status' => 'new']);
            $contact['status'] = 'new';
            $success = 'Contact marked as new!';
            log_message('info', 'Contact marked as new', ['contact_id' => $contactId]);
        }
        
        // Reload contact data
        $contact = $contactModel->find($contactId);
    }
}

// Get name
$name = trim(($contact['firstname'] ?? '') . ' ' . ($contact['lastname'] ?? ''));
if (($name === '' || $name === '0') && isset($contact['name'])) {
    $name = $contact['name'];
}

// Status badge colors
$statusColors = [
    'new' => 'primary',
    'read' => 'info',
    'replied' => 'success',
];

// Set page variables
$pageTitle = 'View Contact Submission';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Contact Submissions', 'url' => BASE_URL . '/admin/contact-submissions.php'],
    ['label' => 'View Submission', 'url' => ''],
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    <main class="admin-main">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><?php echo $pageTitle; ?></h4>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/admin/contact-submissions.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Submissions
                        </a>
                        <a href="mailto:<?php echo e($contact['email']); ?>" class="btn btn-primary ms-2">
                            <i class="bi bi-envelope me-1"></i>Reply via Email
                        </a>
                    </div>
                </div>
                <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i><?php echo e($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif;
 ?>
                        
                        <?php if (isset($errors['general']) && ($errors['general'] !== '' && $errors['general'] !== '0')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i><?php echo e($errors['general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif;
 ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h5 class="mb-3">Contact Information</h5>
                                    <div class="row mb-3">
                                        <div class="col-sm-3 fw-bold">Name:</div>
                                        <div class="col-sm-9"><?php echo e($name); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3 fw-bold">Email:</div>
                                        <div class="col-sm-9">
                                            <a href="mailto:<?php echo e($contact['email']); ?>"><?php echo e($contact['email']); ?></a>
                                        </div>
                                    </div>
                                    <?php if (!empty($contact['phone'])): ?>
                                        <div class="row mb-3">
                                            <div class="col-sm-3 fw-bold">Phone:</div>
                                            <div class="col-sm-9">
                                                <a href="tel:<?php echo e($contact['phone']); ?>"><?php echo e($contact['phone']); ?></a>
                                            </div>
                                        </div>
                                    <?php endif;
 ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-3 fw-bold">Subject:</div>
                                        <div class="col-sm-9"><?php echo e($contact['subject'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3 fw-bold">Submitted:</div>
                                        <div class="col-sm-9"><?php echo formatDate($contact['created_at'], 'F j, Y g:i A'); ?></div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h5 class="mb-3">Message</h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <?php echo nl2br(e($contact['message'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="badge bg-<?php echo $statusColors[$contact['status']] ?? 'secondary'; ?> fs-6">
                                                <?php echo ucfirst((string) $contact['status']); ?>
                                            </span>
                                        </div>
                                        
                                        <form method="POST" action="">
                                            <?php echo csrf_field(); ?>
                                            
                                            <?php if ($contact['status'] !== 'replied'): ?>
                                                <button type="submit" name="action" value="mark_replied" class="btn btn-success btn-sm w-100 mb-2">
                                                    <i class="bi bi-check-circle me-1"></i>Mark as Replied
                                                </button>
                                            <?php endif;
 ?>
                                            
                                            <?php if ($contact['status'] !== 'read'): ?>
                                                <button type="submit" name="action" value="mark_read" class="btn btn-info btn-sm w-100 mb-2">
                                                    <i class="bi bi-eye me-1"></i>Mark as Read
                                                </button>
                                            <?php endif;
 ?>
                                            
                                            <?php if ($contact['status'] !== 'new'): ?>
                                                <button type="submit" name="action" value="mark_new" class="btn btn-warning btn-sm w-100">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Mark as New
                                                </button>
                                            <?php endif;
 ?>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <a href="mailto:<?php echo e($contact['email']); ?>" class="btn btn-primary btn-sm w-100 mb-2">
                                            <i class="bi bi-envelope me-1"></i>Send Email
                                        </a>
                                        <?php if (!empty($contact['phone'])): ?>
                                            <a href="tel:<?php echo e($contact['phone']); ?>" class="btn btn-secondary btn-sm w-100 mb-2">
                                                <i class="bi bi-telephone me-1"></i>Call
                                            </a>
                                        <?php endif;
 ?>
                                        <button class="btn btn-outline-secondary btn-sm w-100 print-trigger">
                                            <i class="bi bi-printer me-1"></i>Print
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

