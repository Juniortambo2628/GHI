<?php

/**
 * Main Configuration File
 * Global Harmony Initiative Website
 */

// Autoload Composer (must be loaded before using Dotenv)
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load environment detection and configuration
$env_config = require_once __DIR__ . '/environment.php';

// Base Paths
define('BASE_PATH', $env_config['root_path']);
define('BASE_URL', $env_config['base_url']);
define('ADMIN_URL', $env_config['admin_url']);
define('SITE_URL', $env_config['site_url']);

// Database Configuration
define('DB_HOST', $env_config['db_host']);
define('DB_NAME', $env_config['db_name']);
define('DB_USER', $env_config['db_user']);
define('DB_PASS', $env_config['db_pass']);
define('DB_CHARSET', $env_config['db_charset']);

// Site Configuration
define('SITE_NAME', $_ENV['SITE_NAME'] ?? 'Global Harmony Initiative Inc.');
define('SITE_TAGLINE', $_ENV['SITE_TAGLINE'] ?? 'Bridging Global Compassion with Local Action');
define('SITE_EMAIL', $_ENV['SITE_EMAIL'] ?? 'info@globalharmonyinitiative.com');
define('SITE_PHONE_US', $_ENV['SITE_PHONE_US'] ?? '+1 (437) 297-7977');
define('SITE_PHONE_EA', $_ENV['SITE_PHONE_EA'] ?? '+254 (xxx) xxx-xxx');

// Path Definitions
define('ASSETS_PATH', BASE_PATH . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('LOGO_PATH', BASE_PATH . '/Logo');

// URL Definitions
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');
define('LOGO_URL', BASE_URL . '/Logo');

// Security
define('ADMIN_SESSION_NAME', $_ENV['ADMIN_SESSION_NAME'] ?? 'ghi_admin_session');
define('SESSION_LIFETIME', (int)($_ENV['SESSION_LIFETIME'] ?? 86400)); // Default: 24 hours

// Email Configuration
define('MAILER_DSN', $_ENV['MAILER_DSN'] ?? 'smtp://localhost:1025');

// Logging Configuration
define('LOG_LEVEL', $_ENV['LOG_LEVEL'] ?? 'debug');
define('LOG_PATH', $_ENV['LOG_PATH'] ?? BASE_PATH . '/logs/app.log');

// Cache Configuration
define('CACHE_PATH', $_ENV['CACHE_PATH'] ?? BASE_PATH . '/cache');

// File Upload Configuration
define('UPLOADS_MAX_SIZE', (int)($_ENV['UPLOADS_MAX_SIZE'] ?? 10485760)); // Default: 10MB
define('UPLOADS_ALLOWED_TYPES', $_ENV['UPLOADS_ALLOWED_TYPES'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx');

// Sentry Configuration (optional)
define('SENTRY_DSN', $_ENV['SENTRY_DSN'] ?? '');

// CSRF Configuration
define('CSRF_TOKEN_NAME', $_ENV['CSRF_TOKEN_NAME'] ?? '_token');

// Timezone
date_default_timezone_set('America/New_York');

// Load constants and functions
require_once BASE_PATH . '/includes/constants.php';
require_once BASE_PATH . '/includes/functions.php';

// Load email configuration
require_once BASE_PATH . '/includes/email-config.php';

// Load event listeners
if (file_exists(BASE_PATH . '/config/events.php')) {
    require_once BASE_PATH . '/config/events.php';
}

// Store environment config globally for easy access
$GLOBALS['env_config'] = $env_config;
