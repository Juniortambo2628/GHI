<?php
/**
 * Security Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\AuthService;
use GHI\Services\ValidationService;

// Check authentication
require_login();

$success = '';
$error = '';
$errors = [];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!csrf_validate($token, 'security')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate current password
        if (empty($currentPassword)) {
            $errors['current_password'] = ['Current password is required'];
        }

        // Validate new password
        $passwordErrors = ValidationService::validatePassword($newPassword);
        if ($passwordErrors !== []) {
            $errors['new_password'] = $passwordErrors;
        }

        // Validate password confirmation
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = ['Passwords do not match'];
        }

        if ($errors === []) {
            // Attempt to change password via AuthService
            try {
                if (AuthService::changePassword($currentPassword, $newPassword)) {
                    $success = 'Password changed successfully!';
                } else {
                    $error = 'Current password is incorrect or password change failed.';
                }
            } catch (\Exception) {
                $error = 'An error occurred while changing your password. Please try again.';
            }
        } else {
            $error = 'Please correct the errors below.';
        }
    }
}

// Set page variables
$pageTitle = 'Security';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/admin/index.php'],
    ['label' => 'Security', 'url' => BASE_URL . '/admin/security.php'],
];

// Include header
require_once __DIR__ . '/includes/header.php';

// Include sidebar
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-wrapper">
    <!-- Hero Area -->
    <?php require_once __DIR__ . '/includes/hero.php'; ?>
    
    <!-- Main Content -->
    <main class="admin-main">
        <div class="container-fluid">
            <?php if ($success !== '' && $success !== '0'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif;
 ?>

            <?php if ($error !== '' && $error !== '0'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif;
 ?>

            <div class="row g-4">
                <!-- Change Password -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-key me-2"></i>Change Password
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="admin-edit-form">
                                <?php echo csrf_field('security'); ?>
                                <input type="hidden" name="action" value="change_password">

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input 
                                        type="password" 
                                        class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>" 
                                        id="current_password" 
                                        name="current_password"
                                        required
                                        autocomplete="current-password"
                                    >
                                    <?php if (isset($errors['current_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo implode('<br>', $errors['current_password']); ?>
                                        </div>
                                    <?php endif;
 ?>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input 
                                        type="password" 
                                        class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" 
                                        id="new_password" 
                                        name="new_password"
                                        required
                                        autocomplete="new-password"
                                    >
                                    <small class="form-text text-muted">
                                        Password must be at least 8 characters long
                                    </small>
                                    <?php if (isset($errors['new_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo implode('<br>', $errors['new_password']); ?>
                                        </div>
                                    <?php endif;
 ?>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input 
                                        type="password" 
                                        class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                        id="confirm_password" 
                                        name="confirm_password"
                                        required
                                        autocomplete="new-password"
                                    >
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo implode('<br>', $errors['confirm_password']); ?>
                                        </div>
                                    <?php endif;
 ?>
                                </div>

                                <button type="submit" class="btn btn-dark">
                                    <i class="bi bi-save me-2"></i>Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Information -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-shield-check me-2"></i>Security Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <h6>Password Requirements</h6>
                            <ul class="mb-4">
                                <li>Minimum 8 characters</li>
                                <li>Use a unique password not used elsewhere</li>
                                <li>Consider using a password manager</li>
                            </ul>

                            <h6>Session Security</h6>
                            <p class="mb-2">
                                <i class="bi bi-clock me-2"></i>
                                Your session will expire after 30 minutes of inactivity.
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-pc-display me-2"></i>
                                View and manage active sessions in the <a href="<?php echo BASE_URL; ?>/admin/sessions.php">Sessions</a> page.
                            </p>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Account Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['user_email'])): ?>
                                <p class="mb-2">
                                    <strong>Email:</strong> <?php echo e($_SESSION['user_email']); ?>
                                </p>
                            <?php endif;
 ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <p class="mb-0">
                                    <strong>User ID:</strong> <?php echo e($_SESSION['user_id']); ?>
                                </p>
                            <?php endif;
 ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

