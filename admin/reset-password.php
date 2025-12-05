<?php
/**
 * Password Reset Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\AuthService;
use GHI\Services\ValidationService;

if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

$selector = $_GET['selector'] ?? $_POST['selector'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$errors = [];
$errorMessage = '';
$tokenValid = false;

if ($selector && $token) {
    $tokenValid = AuthService::canResetPassword($selector, $token);
} else {
    $errorMessage = 'This password reset link is invalid. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
    $csrfToken = $_POST[CSRF_TOKEN_NAME] ?? '';

    if (! csrf_validate($csrfToken)) {
        $errorMessage = 'Invalid security token. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';

        $passwordErrors = ValidationService::validatePassword($password, 8);
        if ($passwordErrors !== []) {
            $errors['password'] = $passwordErrors;
        }

        if ($password !== $confirmPassword) {
            $errors['password_confirmation'][] = 'Passwords do not match';
        }

        if ($errors === []) {
            $reset = AuthService::resetPassword($selector, $token, $password);

            if ($reset) {
                $_SESSION['auth_success'] = 'Your password has been reset. You can now sign in with your new password.';
                header('Location: ' . BASE_URL . '/admin/login.php');
                exit;
            }

            $errorMessage = 'Unable to reset your password. The link may have expired. Please request a new reset link.';
            $tokenValid = false;
        } else {
            $errorMessage = 'Please correct the errors below.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin Dashboard - <?php echo SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/admin/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-form-panel">
            <div class="login-content">
                <div class="login-header">
                    <h1 class="login-title">Set a New Password</h1>
                    <p class="login-subtitle">
                        Choose a strong password for your account.
                    </p>
                </div>

                <?php if ($errorMessage !== '' && $errorMessage !== '0'): ?>
                    <div class="alert alert-<?php echo $tokenValid ? 'danger' : 'warning'; ?>" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif;
 ?>

                <?php if ($tokenValid): ?>
                    <form method="POST" action="" class="login-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="selector" value="<?php echo e($selector); ?>">
                        <input type="hidden" name="token" value="<?php echo e($token); ?>">

                        <div class="form-group">
                            <label for="password" class="form-label">New password</label>
                            <input
                                type="password"
                                class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                                id="password"
                                name="password"
                                placeholder="At least 8 characters"
                                required
                                autocomplete="new-password"
                            >
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo implode('<br>', $errors['password']); ?>
                                </div>
                            <?php endif;
 ?>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm password</label>
                            <input
                                type="password"
                                class="form-control <?php echo isset($errors['password_confirmation']) ? 'is-invalid' : ''; ?>"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Re-enter new password"
                                required
                                autocomplete="new-password"
                            >
                            <?php if (isset($errors['password_confirmation'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo implode('<br>', $errors['password_confirmation']); ?>
                                </div>
                            <?php endif;
 ?>
                        </div>

                        <button type="submit" class="btn btn-signin">Update Password</button>
                    </form>
<?php else: ?>
                    <div class="login-footer mt-4">
                        <p class="signup-link">
                            Need a new link? <a href="<?php echo BASE_URL; ?>/admin/forgot-password.php" class="signup-link-text">Request password reset</a>
                        </p>
                    </div>
                <?php endif;
 ?>

                <div class="login-footer mt-4">
                    <p class="signup-link">
                        <a href="<?php echo BASE_URL; ?>/admin/login.php" class="signup-link-text">Back to login</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="login-image-panel">
            <img src="<?php echo BASE_URL; ?>/Banners-and-portraits/login-portrait.jpg" alt="Reset Password" class="login-image">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

