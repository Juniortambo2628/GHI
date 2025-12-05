<?php
/**
 * Environment Detection
 * Automatically detects development vs production environment
 */

/**
 * Detect current environment based on server characteristics
 * 
 * @return string 'development' or 'production'
 */
function detect_environment() {
    // Check by domain
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Production domains
    $production_domains = [
        'globalharmonyinitiative.com',
        'www.globalharmonyinitiative.com',
    ];
    
    foreach ($production_domains as $domain) {
        if (strpos($host, $domain) !== false) {
            return 'production';
        }
    }
    
    // Check by IP address
    $server_ip = $_SERVER['SERVER_ADDR'] ?? '';
    if ($server_ip === '54.37.142.31') {
        return 'production';
    }
    
    // Check by document root
    $doc_root = $_SERVER['DOCUMENT_ROOT'] ?? '';
    if (strpos($doc_root, '/home/jhoffkau') !== false) {
        return 'production';
    }
    
    // Default to development
    return 'development';
}

/**
 * Check if current environment is production
 * 
 * @return bool
 */
function is_production() {
    return detect_environment() === 'production';
}

/**
 * Check if current environment is development
 * 
 * @return bool
 */
function is_development() {
    return detect_environment() === 'development';
}

/**
 * Get environment-specific configuration
 * 
 * @return array
 */
function get_environment_config() {
    $env = detect_environment();
    
    if ($env === 'production') {
        return [
            'environment' => 'production',
            'debug' => false,
            'display_errors' => false,
            'log_errors' => true,
            'error_log' => '/home/jhoffkau/logs/php_error.log',
            
            // Database
            'db_host' => 'localhost',
            'db_name' => 'jhoffkau_GHI',
            'db_user' => 'jhoffkau_admin',
            'db_pass' => 'GHI@admin2025',
            'db_charset' => 'utf8mb4',
            
            // URLs
            'base_url' => 'https://www.globalharmonyinitiative.com',
            'admin_url' => 'https://www.globalharmonyinitiative.com/admin',
            'site_url' => 'https://www.globalharmonyinitiative.com',
            
            // Email (SMTP)
            'mail_host' => 'mail.globalharmonyinitiative.com',
            'mail_port' => 465,
            'mail_encryption' => 'ssl',
            'mail_username' => 'admin@globalharmonyinitiative.com',
            'mail_password' => 'GHI@admin2025',
            'mail_from' => 'admin@globalharmonyinitiative.com',
            'mail_from_name' => 'Global Harmony Initiative',
            
            // Paths
            'root_path' => '/home/jhoffkau/public_html',
            'upload_path' => '/home/jhoffkau/public_html/uploads',
            'log_path' => '/home/jhoffkau/logs',
            
            // Security
            'force_https' => true,
            'session_secure' => true,
            'cookie_secure' => true,
            'cookie_httponly' => true,
            
            // Cache
            'cache_enabled' => true,
            'cache_lifetime' => 3600, // 1 hour
            
            // Performance
            'gzip_compression' => true,
            'minify_html' => true,
            'minify_css' => true,
            'minify_js' => true,
        ];
    } else {
        // Development configuration
        return [
            'environment' => 'development',
            'debug' => true,
            'display_errors' => true,
            'log_errors' => true,
            'error_log' => 'php_error.log',
            
            // Database
            'db_host' => 'localhost',
            'db_name' => 'global_harmony_initiative',
            'db_user' => 'root',
            'db_pass' => '',
            'db_charset' => 'utf8mb4',
            
            // URLs
            'base_url' => 'http://localhost/GHI',
            'admin_url' => 'http://localhost/GHI/admin',
            'site_url' => 'http://localhost/GHI',
            
            // Email (Local testing - use log file)
            'mail_host' => 'localhost',
            'mail_port' => 1025,
            'mail_encryption' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_from' => 'admin@localhost',
            'mail_from_name' => 'GHI Development',
            
            // Paths
            'root_path' => __DIR__ . '/..',
            'upload_path' => __DIR__ . '/../uploads',
            'log_path' => __DIR__ . '/../logs',
            
            // Security
            'force_https' => false,
            'session_secure' => false,
            'cookie_secure' => false,
            'cookie_httponly' => true,
            
            // Cache
            'cache_enabled' => false,
            'cache_lifetime' => 0,
            
            // Performance
            'gzip_compression' => false,
            'minify_html' => false,
            'minify_css' => false,
            'minify_js' => false,
        ];
    }
}

// Auto-detect and set environment
define('ENVIRONMENT', detect_environment());
define('IS_PRODUCTION', is_production());
define('IS_DEVELOPMENT', is_development());

// Get environment config
$env_config = get_environment_config();

// Set PHP error reporting based on environment
if ($env_config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
}

ini_set('log_errors', $env_config['log_errors'] ? '1' : '0');
if (isset($env_config['error_log'])) {
    ini_set('error_log', $env_config['error_log']);
}

// Return configuration for use in main config
return $env_config;

