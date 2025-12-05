<?php
/**
 * Admin Login Page
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\AuthService;
use GHI\Services\ValidationService;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

$flashSuccess = $_SESSION['auth_success'] ?? '';
$flashError = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_success'], $_SESSION['auth_error']);

// Check if already logged in
if (AuthService::isLoggedIn()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';
$success = '';
$errors = [];

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (! csrf_validate($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate email
        $emailErrors = ValidationService::validateEmail($email);
        if ($emailErrors !== []) {
            $errors['email'] = $emailErrors;
        }

        // Validate password
        $passwordErrors = ValidationService::validateRequired($password, 'Password');
        if ($passwordErrors !== []) {
            $errors['password'] = $passwordErrors;
        }

        // If validation passes, attempt login
        if ($errors === []) {
            $rememberDuration = isset($_POST['remember']) ? 60 * 60 * 24 * 30 : null; // 30 days if remember me
            
            if (AuthService::login($email, $password, $rememberDuration)) {
                header('Location: ' . BASE_URL . '/admin/index.php');
                exit;
            }

            $error = 'Invalid email or password.';
        } else {
            $error = 'Please correct the errors below.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Dashboard - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Login Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/admin/css/login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Login Form -->
        <div class="login-form-panel">
            <div class="login-content">
                <!-- Header -->
                <div class="login-header">
                    <h1 class="login-title">Welcome Back <span class="wave-emoji">👋</span></h1>
                    <p class="login-subtitle">Today is a new day. It's your day. You shape it. Sign in to start managing your projects.</p>
                </div>
                
                <!-- Error/Success Messages -->
                <?php if ($flashSuccess): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars((string) $flashSuccess); ?>
                    </div>
                <?php endif;
 ?>

                <?php if ($flashError): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars((string) $flashError); ?>
                    </div>
                <?php endif;
 ?>

                <?php if ($error !== '' && $error !== '0'): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif;
 ?>
                
                <?php if ($success !== ''): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif;
 ?>
                
                <!-- Login Form -->
                <form method="POST" action="" class="login-form">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                            id="email" 
                            name="email" 
                            placeholder="Example@email.com" 
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
                    
                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="form-label">Password</label>
                            <a href="<?php echo BASE_URL; ?>/admin/forgot-password.php" class="forgot-password-link">Forgot Password?</a>
                        </div>
                        <input 
                            type="password" 
                            class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                            id="password" 
                            name="password" 
                            placeholder="At least 8 characters" 
                            required
                            autocomplete="current-password"
                        >
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback">
                                <?php echo implode('<br>', $errors['password']); ?>
                            </div>
                        <?php endif;
 ?>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>
                    
                    <!-- Sign In Button -->
                    <button type="submit" class="btn btn-signin">Sign in</button>
                </form>
                
                <!-- Footer Links -->
                <div class="login-footer">
                    <p class="copyright">© <?php echo date('Y'); ?> ALL RIGHTS RESERVED</p>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Image -->
        <div class="login-image-panel">
            <img src="<?php echo BASE_URL; ?>/Banners-and-portraits/login-portrait.jpg" alt="Login Background" class="login-image">
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

