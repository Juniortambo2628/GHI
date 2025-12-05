<?php

/**
 * Admin Logout
 * Global Harmony Initiative Admin Dashboard
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\AuthService;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Logout using AuthService
AuthService::logout();

// Redirect to login
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
