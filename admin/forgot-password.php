<?php
/**
 * Forgot Password Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\AuthService;
use GHI\Services\ValidationService;

if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

$errors = [];
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';

    if (! csrf_validate($token)) {
        $errorMessage = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $emailErrors = ValidationService::validateEmail($email);

        if ($emailErrors !== []) {
            $errors['email'] = $emailErrors;
            $errorMessage = 'Please correct the errors below.';
        } else {
            $result = AuthService::requestPasswordReset($email);

            if ($result) {
                $_SESSION['auth_success'] = 'If an account exists for that email, a password reset link has been sent.';
                header('Location: ' . BASE_URL . '/admin/login.php');
                exit;
            }

            $errorMessage = 'Too many password reset attempts. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Admin Dashboard - <?php echo SITE_NAME; ?></title>

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
                    <h1 class="login-title">Reset Your Password</h1>
                    <p class="login-subtitle">
                        Enter the email associated with your account and we’ll send you a link to reset your password.
                    </p>
                </div>

                <?php if ($errorMessage !== '' && $errorMessage !== '0'): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif;
 ?>

                <form method="POST" action="" class="login-form">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="email" class="form-label">Email address</label>
                        <input
                            type="email"
                            class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                            id="email"
                            name="email"
                            placeholder="you@example.com"
                            value="<?php echo e($_POST['email'] ?? ''); ?>"
                            required
                            autocomplete="email"
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo implode('<br>', $errors['email']); ?>
                            </div>
                        <?php endif;
 ?>
                    </div>

                    <button type="submit" class="btn btn-signin">Send Reset Link</button>
                </form>

                <div class="login-footer mt-4">
                    <p class="signup-link">
                        Remembered your password? <a href="<?php echo BASE_URL; ?>/admin/login.php" class="signup-link-text">Back to login</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="login-image-panel">
            <img src="<?php echo BASE_URL; ?>/Banners-and-portraits/login-portrait.jpg" alt="Password Reset" class="login-image">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

