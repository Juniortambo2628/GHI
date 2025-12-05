<?php

/**
 * Authentication Check
 * Include this file at the top of admin pages that require authentication
 */

if (! defined('SITE_NAME')) {
    require_once __DIR__ . '/../../config/config.php';
}

use GHI\Services\AuthService;

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(ADMIN_SESSION_NAME);
    session_start();
}

// Require login - redirects to login page if not authenticated
AuthService::requireLogin(BASE_URL . '/admin/login.php');
