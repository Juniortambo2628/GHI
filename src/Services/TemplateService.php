<?php

/**
 * Template Service using Twig
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class TemplateService
{
    private static ?Environment $instance = null;

    private static array $globalVariables = [];

    /**
     * Get Twig Environment instance (Singleton)
     */
    public static function getInstance(): Environment
    {
        if (!self::$instance instanceof \Twig\Environment) {
            self::$instance = self::createEnvironment();
        }

        return self::$instance;
    }

    /**
     * Create and configure Twig Environment
     */
    private static function createEnvironment(): Environment
    {
        $templatesPath = defined('BASE_PATH') ? BASE_PATH . '/templates' : __DIR__ . '/../../templates';

        // Create templates directory if it doesn't exist
        if (! is_dir($templatesPath)) {
            mkdir($templatesPath, 0755, true);
            mkdir($templatesPath . '/layouts', 0755, true);
            mkdir($templatesPath . '/components', 0755, true);
            mkdir($templatesPath . '/pages', 0755, true);
        }

        $loader = new FilesystemLoader($templatesPath);

        $options = [
            'cache' => defined('CACHE_PATH') ? CACHE_PATH . '/twig' : __DIR__ . '/../../cache/twig',
            'auto_reload' => defined('ENVIRONMENT') && ENVIRONMENT === 'development',
            'debug' => defined('ENVIRONMENT') && ENVIRONMENT === 'development',
        ];

        $twig = new Environment($loader, $options);

        // Add debug extension in development
        if ($options['debug']) {
            $twig->addExtension(new DebugExtension());
        }

        // Set global variables
        $twig->addGlobal('site_name', defined('SITE_NAME') ? SITE_NAME : 'Global Harmony Initiative');
        $twig->addGlobal('site_tagline', defined('SITE_TAGLINE') ? SITE_TAGLINE : '');
        $twig->addGlobal('base_url', defined('BASE_URL') ? BASE_URL : 'http://localhost');
        $twig->addGlobal('assets_url', defined('ASSETS_URL') ? ASSETS_URL : '');
        $twig->addGlobal('uploads_url', defined('UPLOADS_URL') ? UPLOADS_URL : '');

        // Add custom functions
        $twig->addFunction(new \Twig\TwigFunction('csrf_field', fn($tokenId = 'form'): string => csrf_field($tokenId)));

        $twig->addFunction(new \Twig\TwigFunction('csrf_token', fn($tokenId = 'form'): string => csrf_token($tokenId)));

        $twig->addFunction(new \Twig\TwigFunction('format_date', fn($date, $format = 'F j, Y'): string => formatDate($date, $format)));

        $twig->addFunction(new \Twig\TwigFunction('truncate', fn($text, $length = 100): string => truncate($text, $length)));

        // Add custom filters
        $twig->addFilter(new \Twig\TwigFilter('slug', fn($string): string => generateSlug($string)));

        return $twig;
    }

    /**
     * Render template
     */
    public static function render(string $template, array $variables = []): string
    {
        try {
            $twig = self::getInstance();

            // Merge with global variables
            $variables = array_merge(self::$globalVariables, $variables);

            return $twig->render($template, $variables);
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Template render failed', [
                    'template' => $template,
                    'error' => $exception->getMessage(),
                ]);
            }

            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                throw $exception;
            }

            return '<!-- Template rendering error -->';
        }
    }

    /**
     * Set global variable
     */
    public static function setGlobal(string $name, $value): void
    {
        self::$globalVariables[$name] = $value;
        if (self::$instance instanceof \Twig\Environment) {
            self::$instance->addGlobal($name, $value);
        }
    }

    /**
     * Display template (echo instead of return)
     */
    public static function display(string $template, array $variables = []): void
    {
        echo self::render($template, $variables);
    }
}
