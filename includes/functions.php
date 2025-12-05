<?php

/**
 * Helper Functions
 * Global Harmony Initiative Website
 */

use GHI\Services\LoggerService;
use GHI\Services\CsrfService;
use GHI\Services\FileService;
use GHI\Services\AuthService;
use GHI\Services\ValidationService;
use GHI\Services\MailService;
use GHI\Services\TemplateService;
use GHI\Services\CacheService;
use GHI\Services\EventService;
use Symfony\Component\VarDumper\VarDumper;
use GHI\Services\HttpService;
use GHI\Services\SiteSettingsService;
use Cocur\Slugify\Slugify;

/**
 * Escape HTML output
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Log a message using Monolog
 */
function log_message(string $level, string $message, array $context = []): void
{
    try {
        $logger = LoggerService::getInstance();
        $logger->log(
            match (strtolower($level)) {
                'debug' => \Monolog\Level::Debug,
                'info' => \Monolog\Level::Info,
                'notice' => \Monolog\Level::Notice,
                'warning' => \Monolog\Level::Warning,
                'error' => \Monolog\Level::Error,
                'critical' => \Monolog\Level::Critical,
                'alert' => \Monolog\Level::Alert,
                'emergency' => \Monolog\Level::Emergency,
                default => \Monolog\Level::Info,
            },
            $message,
            $context
        );
    } catch (\Exception $exception) {
        // Fallback to error_log if logger fails
        error_log('Logger error: ' . $exception->getMessage());
        error_log(sprintf('Original message: [%s] %s', $level, $message));
    }
}

/**
 * Generate CSRF token
 */
function csrf_token(string $tokenId = 'form'): string
{
    return CsrfService::generateToken($tokenId);
}

/**
 * Get CSRF token field HTML
 */
function csrf_field(string $tokenId = 'form'): string
{
    return CsrfService::getTokenField($tokenId);
}

/**
 * Validate CSRF token
 */
function csrf_validate(string $token, string $tokenId = 'form'): bool
{
    return CsrfService::validateToken($token, $tokenId);
}

/**
 * Get current page name
 */
function getCurrentPage(): string
{
    $page = basename((string) $_SERVER['PHP_SELF'], '.php');

    return $page === 'index' ? 'home' : $page;
}

/**
 * Check if page is active for navigation
 */
function isActivePage(string $page): bool
{
    $current = getCurrentPage();

    return $current === $page;
}

/**
 * Format date string
 */
function formatDate(string $date, string $format = 'F j, Y'): string
{
    $timestamp = strtotime($date);

    return $timestamp ? date($format, $timestamp) : $date;
}

/**
 * Generate URL-friendly slug using cocur/slugify
 */
function generateSlug(string $string): string
{
    static $slugify = null;
    
    if ($slugify === null) {
        $slugify = new Slugify();
    }
    
    return $slugify->slugify($string);
}

/**
 * Map objective slug from URL to objective value
 * Handles both full slugs (e.g., "community-empowerment") and direct values (e.g., "empowerment")
 *
 * @param string $slug Objective slug from URL
 * @return string|null Objective value or null if not found
 */
function mapObjectiveSlug(string $slug): ?string
{
    $objectiveMap = [
        // Full slugs (from objective titles)
        'poverty-alleviation-livelihoods' => 'poverty',
        'education-access-youth-development' => 'education',
        'health-well-being' => 'health',
        'community-empowerment' => 'empowerment',
        'global-partnerships-awareness' => 'partnerships',
        // Direct values (also supported)
        'poverty' => 'poverty',
        'education' => 'education',
        'health' => 'health',
        'empowerment' => 'empowerment',
        'partnerships' => 'partnerships',
    ];
    
    return $objectiveMap[strtolower(trim($slug))] ?? null;
}

/**
 * Truncate text with ellipsis
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Truncate text by word count
 */
function truncateWords(string $text, int $wordCount = 5, string $suffix = '...'): string
{
    $words = explode(' ', trim($text));
    if (count($words) <= $wordCount) {
        return $text;
    }

    return implode(' ', array_slice($words, 0, $wordCount)) . $suffix;
}

/**
 * Get image URL with fallback
 */
function getImageUrl(?string $filename, string $fallback = 'pexels-lagosfoodbank-6472487.jpg'): string
{
    if ($filename === null || $filename === '' || $filename === '0') {
        return BASE_URL . '/Banners-and-portraits/' . $fallback;
    }

    // Check if file exists in uploads folder
    $normalized = ltrim($filename, '/');
    if (file_exists(UPLOADS_PATH . '/' . $normalized)) {
        return UPLOADS_URL . '/' . $normalized;
    }

    // Check nested images directory (FilePond uploads)
    if (file_exists(UPLOADS_PATH . '/images/' . $normalized)) {
        return UPLOADS_URL . '/images/' . $normalized;
    }

    // Check if file exists in Banners-and-portraits folder
    $bannersPath = BASE_PATH . '/Banners-and-portraits/' . $filename;
    if (file_exists($bannersPath)) {
        return BASE_URL . '/Banners-and-portraits/' . $filename;
    }

    // Fallback to default image
    return BASE_URL . '/Banners-and-portraits/' . $fallback;
}

/**
 * Build action menu definition array
 *
 * @return array<int, array<string, mixed>>
 */
function build_action_menu(?string $viewUrl, ?string $editUrl, ?string $deleteUrl): array
{
    $actions = [];

    if ($viewUrl !== null && $viewUrl !== '' && $viewUrl !== '0') {
        $actions[] = [
            'label' => 'View details',
            'icon' => 'bi-eye',
            'url' => $viewUrl,
            'type' => 'view',
        ];
    }

    if ($editUrl !== null && $editUrl !== '' && $editUrl !== '0') {
        $actions[] = [
            'label' => 'Edit',
            'icon' => 'bi-pencil',
            'url' => $editUrl,
            'type' => 'edit',
        ];
    }

    if ($deleteUrl !== null && $deleteUrl !== '' && $deleteUrl !== '0') {
        $actions[] = [
            'label' => 'Delete',
            'icon' => 'bi-trash',
            'url' => $deleteUrl,
            'type' => 'delete',
            'danger' => true,
            'confirm' => 'Are you sure you want to delete this item?',
        ];
    }

    return $actions;
}

/**
 * Resolve a stored site setting value.
 */
function site_setting(string $key, mixed $default = null): mixed
{
    return SiteSettingsService::getInstance()->get($key, $default);
}

/**
 * Resolve a URL (accepts relative paths or absolute URLs).
 */
function resolve_url(?string $path): string
{
    if ($path === null || $path === '' || $path === '0') {
        return BASE_URL;
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get responsive image with srcset and WebP support
 *
 * @param string|null $filename Image filename
 * @param array $options Options (width, height, sizes, alt, class, loading, fetchpriority)
 * @return string HTML img tag with responsive attributes
 */
function getResponsiveImage(?string $filename, array $options = []): string
{
    $defaultOptions = [
        'width' => null,
        'height' => null,
        'sizes' => '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw',
        'alt' => '',
        'class' => 'img-fluid',
        'loading' => 'lazy',
        'fetchpriority' => null,
        'fallback' => 'pexels-lagosfoodbank-6472487.jpg',
    ];

    $options = array_merge($defaultOptions, $options);
    $imageUrl = getImageUrl($filename, $options['fallback']);

    // Build attributes
    $attrs = [];

    if ($options['width']) {
        $attrs[] = 'width="' . htmlspecialchars((string) $options['width'], ENT_QUOTES) . '"';
    }

    if ($options['height']) {
        $attrs[] = 'height="' . htmlspecialchars((string) $options['height'], ENT_QUOTES) . '"';
    }

    if ($options['alt']) {
        $attrs[] = 'alt="' . htmlspecialchars((string) $options['alt'], ENT_QUOTES) . '"';
    }

    if ($options['class']) {
        $attrs[] = 'class="' . htmlspecialchars((string) $options['class'], ENT_QUOTES) . '"';
    }

    if ($options['loading']) {
        $attrs[] = 'loading="' . htmlspecialchars((string) $options['loading'], ENT_QUOTES) . '"';
    }

    if ($options['fetchpriority']) {
        $attrs[] = 'fetchpriority="' . htmlspecialchars((string) $options['fetchpriority'], ENT_QUOTES) . '"';
    }

    // Try to generate WebP and responsive sizes (only if ImageService is available)
    $webpUrl = null;
    $srcset = null;
    $responsiveSizes = [];

    // Static counter to limit concurrent image processing (prevents Chrome crashes)
    static $imageProcessingCount = 0;
    static $maxConcurrentProcessing = 1; // Limit to 1 image processing at once (aggressive limit)
    static $totalProcessedThisRequest = 0;
    static $maxImagesPerRequest = 3; // Maximum images to optimize per page load (reduced to prevent crashes)
    static $optimizationEnabled = true; // Can be disabled to skip all optimization

    // Skip optimization entirely if disabled or too many images already processed
    // This prevents browser crashes from excessive image processing
    if ($optimizationEnabled && $totalProcessedThisRequest < $maxImagesPerRequest) {
        try {
            if (class_exists(\GHI\Services\ImageService::class)) {
                $imageService = new \GHI\Services\ImageService();

                // Determine image path
                $imagePath = null;
                if ($filename && file_exists(UPLOADS_PATH . '/' . $filename)) {
                    $imagePath = 'uploads/images/' . $filename;
                } elseif ($filename && file_exists(BASE_PATH . '/Banners-and-portraits/' . $filename)) {
                    $imagePath = 'Banners-and-portraits/' . $filename;
                }

                if ($imagePath !== null && $imagePath !== '' && $imagePath !== '0') {
                    // Skip processing if we've already processed too many images this request
                    // This prevents browser crashes from too many image operations
                    if ($totalProcessedThisRequest >= $maxImagesPerRequest) {
                        // Use original image without optimization
                        // This is fine - page will still render correctly
                        if (function_exists('log_message')) {
                            log_message('debug', 'Image processing skipped - max images per request reached', [
                                'path' => $imagePath,
                                'count' => $totalProcessedThisRequest
                            ]);
                        }
                    } elseif ($imageProcessingCount >= $maxConcurrentProcessing) {
                        // Skip processing if too many images are already being processed
                        // This prevents memory exhaustion and Chrome crashes
                        if (function_exists('log_message')) {
                            log_message('debug', 'Image processing skipped - too many concurrent operations', [
                                'path' => $imagePath,
                                'count' => $imageProcessingCount
                            ]);
                        }
                    } else {
                        $imageProcessingCount++;
                        $totalProcessedThisRequest++;

                        try {
                            // Generate WebP version (with error handling for memory issues)
                            try {
                                $webpPath = $imageService->convertToWebP($imagePath, 85, 1080);
                                if ($webpPath !== null && $webpPath !== '' && $webpPath !== '0') {
                                    $webpUrl = $webpPath;
                                }
                            } catch (\Exception $e) {
                                // Silently fail WebP conversion - will use original image
                                if (function_exists('log_message')) {
                                    log_message('warning', 'WebP conversion skipped', [
                                        'path' => $imagePath,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }

                            // Generate responsive sizes for srcset (with error handling)
                            try {
                                $responsiveSizes = $imageService->generateResponsiveSizes($imagePath, [400, 600, 800, 1080]);
                                if ($responsiveSizes !== []) {
                                    $srcsetParts = [];
                                    foreach ($responsiveSizes as $size) {
                                        $srcsetParts[] = $size['url'] . ' ' . $size['width'] . 'w';
                                    }

                                    $srcset = implode(', ', $srcsetParts);
                                }
                            } catch (\Exception $e) {
                                // Silently fail responsive sizes - will use original image
                                if (function_exists('log_message')) {
                                    log_message('warning', 'Responsive sizes generation skipped', [
                                        'path' => $imagePath,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }
                        } finally {
                            // Always decrement counter, even on error
                            $imageProcessingCount--;
                        }
                    }
                }
            }
        } catch (\Exception) {
            // Silently fail - use original image
            // This ensures the page still renders even if image optimization fails
            if (isset($imageProcessingCount) && $imageProcessingCount > 0) {
                $imageProcessingCount--;
            }
        }
    }

    // Build picture element if WebP is available, otherwise use img
    if ($webpUrl !== null && $webpUrl !== '' && $webpUrl !== '0') {
        $picture = '<picture>';

        // WebP source
        if ($srcset !== null && $srcset !== '' && $srcset !== '0') {
            // Generate WebP srcset
            $webpSrcsetParts = [];
            foreach ($responsiveSizes as $size) {
                $webpPathInfo = pathinfo((string) $size['path']);
                $webpSizePath = $webpPathInfo['dirname'] . '/' . $webpPathInfo['filename'] . '.webp';
                if (file_exists(BASE_PATH . '/' . $webpSizePath)) {
                    $webpSizeUrl = str_replace(BASE_PATH, BASE_URL, $webpSizePath);
                    $webpSrcsetParts[] = $webpSizeUrl . ' ' . $size['width'] . 'w';
                }
            }

            if ($webpSrcsetParts !== []) {
                $picture .= '<source type="image/webp" srcset="' . htmlspecialchars(implode(', ', $webpSrcsetParts), ENT_QUOTES) . '" sizes="' . htmlspecialchars((string) $options['sizes'], ENT_QUOTES) . '">';
            } else {
                $picture .= '<source type="image/webp" srcset="' . htmlspecialchars($webpUrl, ENT_QUOTES) . '">';
            }
        } else {
            $picture .= '<source type="image/webp" srcset="' . htmlspecialchars($webpUrl, ENT_QUOTES) . '">';
        }

        // Fallback img tag
        $picture .= '<img src="' . htmlspecialchars($imageUrl, ENT_QUOTES) . '"';
        if ($srcset !== null && $srcset !== '' && $srcset !== '0') {
            $picture .= ' srcset="' . htmlspecialchars($srcset, ENT_QUOTES) . '" sizes="' . htmlspecialchars((string) $options['sizes'], ENT_QUOTES) . '"';
        }

        $picture .= ' ' . implode(' ', $attrs) . '>';

        return $picture . '</picture>';
    }

    // Standard img tag
    $img = '<img src="' . htmlspecialchars($imageUrl, ENT_QUOTES) . '"';
    if ($srcset !== null && $srcset !== '' && $srcset !== '0') {
        $img .= ' srcset="' . htmlspecialchars($srcset, ENT_QUOTES) . '" sizes="' . htmlspecialchars((string) $options['sizes'], ENT_QUOTES) . '"';
    }

    return $img . (' ' . implode(' ', $attrs) . '>');
}

/**
 * Get logo placeholder URL
 */
function getLogoPlaceholder(): string
{
    return LOGO_URL . '/Square-White-BG.png';
}

/**
 * Upload file using FileService
 */
function upload_file(array $file, string $destinationPath, array $allowedTypes = []): array
{
    return FileService::upload($file, $destinationPath, $allowedTypes);
}

/**
 * Check if file exists using FileService
 */
function file_exists_fs(string $path): bool
{
    return FileService::exists($path);
}

/**
 * Delete file using FileService
 */
function delete_file_fs(string $path): bool
{
    return FileService::delete($path);
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool
{
    return AuthService::isLoggedIn();
}

/**
 * Get current user ID
 */
function get_user_id(): ?int
{
    return AuthService::getUserId();
}

/**
 * Get current user email
 */
function get_user_email(): ?string
{
    return AuthService::getEmail();
}

/**
 * Require login - redirect if not logged in
 */
function require_login(?string $redirectUrl = null): void
{
    AuthService::requireLogin($redirectUrl);
}

/**
 * Validate email
 */
function validate_email(string $email): array
{
    return ValidationService::validateEmail($email);
}

/**
 * Validate data against rules
 */
function validate(array $data, array $rules): array
{
    return ValidationService::validate($data, $rules);
}

/**
 * Send email
 */
function send_email(
    string $to,
    string $subject,
    string $body,
    ?string $from = null,
    ?string $fromName = null,
    bool $isHtml = true
): bool {
    return MailService::send($to, $subject, $body, $from, $fromName, $isHtml);
}

/**
 * Send contact form email
 */
function send_contact_email(string $name, string $email, string $subject, string $message): bool
{
    return MailService::sendContactForm($name, $email, $subject, $message);
}

/**
 * Render Twig template
 */
function render_template(string $template, array $variables = []): string
{
    return TemplateService::render($template, $variables);
}

/**
 * Display Twig template
 */
function display_template(string $template, array $variables = []): void
{
    TemplateService::display($template, $variables);
}

/**
 * Get from cache or execute callback
 */
function cache_get(string $key, ?callable $callback = null, ?int $lifetime = null): mixed
{
    return CacheService::get($key, $callback, $lifetime);
}

/**
 * Set cache value
 */
function cache_set(string $key, mixed $value, ?int $lifetime = null): bool
{
    return CacheService::set($key, $value, $lifetime);
}

/**
 * Delete from cache
 */
function cache_delete(string $key): bool
{
    return CacheService::delete($key);
}

/**
 * Check if cache has key
 */
function cache_has(string $key): bool
{
    return CacheService::has($key);
}

/**
 * Clear all cache
 */
function cache_clear(): bool
{
    return CacheService::clear();
}

/**
 * Remember - Get from cache or execute and cache
 */
function cache_remember(string $key, callable $callback, ?int $lifetime = null): mixed
{
    return CacheService::remember($key, $callback, $lifetime);
}

/**
 * Dispatch event
 */
function event_dispatch(object $event, ?string $eventName = null): object
{
    return EventService::dispatch($event, $eventName);
}

/**
 * Listen to event
 */
function event_listen(string $eventName, callable $listener, int $priority = 0): void
{
    EventService::listen($eventName, $listener, $priority);
}

/**
 * Make HTTP GET request
 */
function http_get(string $url, array $options = []): array
{
    return HttpService::get($url, $options);
}

/**
 * Make HTTP POST request
 */
function http_post(string $url, array $data = [], array $options = []): array
{
    return HttpService::post($url, $data, $options);
}

/**
 * Download file via HTTP
 */
function http_download(string $url, string $destination): bool
{
    return HttpService::download($url, $destination);
}

/**
 * Dump variable(s) using Symfony VarDumper (development only)
 * Usage: dump($var1, $var2, ...)
 * Note: Only declare if Symfony's dump() doesn't exist
 */
if (! function_exists('dump')) {
    function dump(mixed ...$vars): mixed
    {
        if (ENVIRONMENT !== 'development') {
            return null;
        }

        if ($vars === []) {
            VarDumper::dump(new \Symfony\Component\VarDumper\Caster\ScalarStub('🐛'));
            return null;
        }

        if (count($vars) === 1) {
            VarDumper::dump($vars[0]);
            return $vars[0];
        }

        foreach ($vars as $k => $v) {
            VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
        }

        return $vars;
    }
}

/**
 * Dump and die using Symfony VarDumper (development only)
 * Usage: dd($var1, $var2, ...)
 * Note: Only declare if Symfony's dd() doesn't exist
 */
if (! function_exists('dd')) {
    function dd(mixed ...$vars): never
    {
        if (ENVIRONMENT !== 'development') {
            exit(1);
        }

        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        if (count($vars) === 1) {
            VarDumper::dump($vars[0]);
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, is_int($k) ? 1 + $k : $k);
            }
        }

        exit(1);
    }
}

/**
 * Prepare table data for Tabulator
 * Converts array of records to Tabulator-friendly format with action URLs
 */
function prepare_table_data(array $records, ?string $editUrlPattern = null, ?string $deleteUrlPattern = null, ?string $viewUrlPattern = null): array
{
    return array_map(function (array $record) use ($editUrlPattern, $deleteUrlPattern, $viewUrlPattern) {
        $data = $record;

        $viewUrl = null;
        $editUrl = null;
        $deleteUrl = null;

        // Add action URLs
        if ($editUrlPattern !== null && $editUrlPattern !== '' && $editUrlPattern !== '0') {
            $editUrl = str_replace('{id}', $record['id'], $editUrlPattern);
            $data['edit_url'] = $editUrl;
        }

        if ($deleteUrlPattern !== null && $deleteUrlPattern !== '' && $deleteUrlPattern !== '0') {
            $deleteUrl = str_replace('{id}', $record['id'], $deleteUrlPattern);
            $data['delete_url'] = $deleteUrl;
        }

        if ($viewUrlPattern !== null && $viewUrlPattern !== '' && $viewUrlPattern !== '0') {
            $viewUrl = str_replace('{id}', $record['id'], $viewUrlPattern);
            $data['view_url'] = $viewUrl;
        }

        $data['action_menu'] = build_action_menu($viewUrl, $editUrl, $deleteUrl);

        // Escape HTML in string fields
        foreach ($data as $key => $value) {
            if (is_string($value) && !in_array($key, ['edit_url', 'delete_url', 'view_url', 'image', 'slug'])) {
                $data[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }

        return $data;
    }, $records);
}

/**
 * Get Tabulator columns configuration as JSON
 */
function get_tabulator_columns_json(array $columns): string
{
    return json_encode($columns, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
}

/**
 * Get Vite asset URL by pattern (for CSS/JS files with hashes)
 */
function get_vite_asset(string $pattern, string $directory = 'dist/assets'): ?string
{
    $assetsPath = BASE_PATH . '/' . $directory;
    if (!is_dir($assetsPath)) {
        return null;
    }
    
    $files = glob($assetsPath . '/' . $pattern);
    if ($files === [] || $files === false) {
        return null;
    }
    
    // Get the most recent file if multiple matches
    $file = end($files);
    $relativePath = str_replace(BASE_PATH . '/', '', $file);
    
    return BASE_URL . '/' . $relativePath;
}
