<?php

namespace GHI\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Site Settings Service
 * Provides a centralized key/value store with JSON support and CSS overrides.
 */
class SiteSettingsService
{
    private static ?self $instance = null;

    private readonly Connection $db;

    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * @var array<string, mixed>
     */
    private array $defaults = [];

    private bool $tableEnsured = false;

    private readonly string $themeCssPath;

    private function __construct()
    {
        $this->db = DatabaseService::getConnection();
        $this->defaults = $this->loadDefaults();
        $this->themeCssPath = BASE_PATH . '/css/site-theme.css';
        $this->ensureTable();
        $this->seedDefaults();
        $this->ensureThemeStyles();
    }

    public static function getInstance(): self
    {
        if (! self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get a setting value (falls back to default and seeds if missing)
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $record = $this->db->fetchAssociative(
            'SELECT setting_value, setting_type FROM site_settings WHERE setting_key = ? LIMIT 1',
            [$key]
        );

        if (! $record) {
            $value = $default ?? ($this->defaults[$key] ?? null);
            if ($value !== null) {
                $this->set($key, $value, $this->inferGroup($key), false);
            }

            return $this->cache[$key] = $value;
        }

        $value = $this->castValue($record['setting_value'], $record['setting_type']);

        return $this->cache[$key] = $value;
    }

    /**
     * Get multiple settings at once.
     *
     * @param array<string> $keys
     * @return array<string, mixed>
     */
    public function getMany(array $keys): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }

        return $results;
    }

    /**
     * Persist a setting value.
     */
    public function set(string $key, mixed $value, string $group = 'general', bool $regenerateTheme = true): bool
    {
        $type = $this->determineType($value);
        $persistedValue = $this->prepareValueForStorage($value, $type);

        $params = [
            'key' => $key,
            'value' => $persistedValue,
            'group' => $group,
            'type' => $type,
        ];

        $sql = <<<SQL
INSERT INTO site_settings (setting_key, setting_value, setting_group, setting_type, created_at, updated_at)
VALUES (:key, :value, :group, :type, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value),
    setting_group = VALUES(setting_group),
    setting_type = VALUES(setting_type),
    updated_at = NOW()
SQL;

        try {
            $this->db->executeStatement($sql, $params);
            $this->cache[$key] = $value;

            if ($regenerateTheme && $this->shouldRegenerateTheme($key)) {
                $this->generateThemeStyles();
            }

            return true;
        } catch (Exception $exception) {
            log_message('error', 'Site setting save failed', [
                'key' => $key,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get a default value without mutating storage.
     */
    public function getDefault(string $key, mixed $fallback = null): mixed
    {
        return $this->defaults[$key] ?? $fallback;
    }

    /**
     * Return all defaults.
     *
     * @return array<string, mixed>
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Ensure DB table exists.
     */
    private function ensureTable(): void
    {
        if ($this->tableEnsured) {
            return;
        }

        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS site_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(150) NOT NULL UNIQUE,
    setting_value LONGTEXT NULL,
    setting_group VARCHAR(100) NOT NULL DEFAULT 'general',
    setting_type VARCHAR(50) NOT NULL DEFAULT 'text',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        try {
            $this->db->executeStatement($sql);
            $this->tableEnsured = true;
        } catch (Exception $exception) {
            log_message('error', 'Failed to ensure site_settings table', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Seed default values when missing.
     */
    private function seedDefaults(): void
    {
        foreach ($this->defaults as $key => $value) {
            $exists = $this->db->fetchOne(
                'SELECT COUNT(*) FROM site_settings WHERE setting_key = ?',
                [$key]
            );

            if ((int) $exists === 0) {
                $this->set($key, $value, $this->inferGroup($key), false);
            }
        }
    }

    /**
     * Load defaults from config file.
     *
     * @return array<string, mixed>
     */
    private function loadDefaults(): array
    {
        $defaultsPath = BASE_PATH . '/config/site-settings.php';
        if (file_exists($defaultsPath)) {
            $data = require $defaultsPath;
            if (is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    private function determineType(mixed $value): string
    {
        return match (true) {
            is_array($value), is_object($value) => 'json',
            is_bool($value) => 'boolean',
            is_numeric($value) => 'number',
            default => 'text',
        };
    }

    private function prepareValueForStorage(mixed $value, string $type): string
    {
        return match ($type) {
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    private function castValue(?string $value, ?string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'json' => $this->decodeJson($value),
            'boolean' => $value === '1' || strtolower($value) === 'true',
            'number' => is_numeric($value) ? $value + 0 : $value,
            default => $value,
        };
    }

    private function decodeJson(string $value): array
    {
        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return [];
    }

    private function inferGroup(string $key): string
    {
        if (str_starts_with($key, 'hero_')) {
            return 'hero';
        }

        if (str_starts_with($key, 'quote_')) {
            return 'quote';
        }

        return 'general';
    }

    private function shouldRegenerateTheme(string $key): bool
    {
        if ($key === 'quote_banner_background') {
            return true;
        }

        return str_starts_with($key, 'hero_') && $key !== 'hero_home_slides';
    }

    /**
     * Build CSS variables file for dynamic backgrounds.
     */
    private function generateThemeStyles(): void
    {
        $variables = [
            '--quote-banner-bg' => $this->buildCssUrl($this->get('quote_banner_background')),
            '--hero-causes-bg' => $this->buildCssUrl($this->getHeroBackground('hero_causes')),
            '--hero-initiatives-bg' => $this->buildCssUrl($this->getHeroBackground('hero_initiatives')),
            '--hero-events-bg' => $this->buildCssUrl($this->getHeroBackground('hero_events')),
            '--hero-impact-bg' => $this->buildCssUrl($this->getHeroBackground('hero_impact')),
            '--hero-stories-bg' => $this->buildCssUrl($this->getHeroBackground('hero_stories')),
        ];

        $cssLines = [
            '/**',
            ' * Auto-generated theme overrides (do not edit manually).',
            ' * Last updated: ' . date('c'),
            ' */',
            ':root {',
        ];

        foreach ($variables as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($value === '') {
                continue;
            }

            if ($value === '0') {
                continue;
            }

            $cssLines[] = sprintf('  %s: %s;', $key, $value);
        }

        $cssLines[] = '}';
        $cssLines[] = '';

        try {
            file_put_contents($this->themeCssPath, implode(PHP_EOL, $cssLines));
        } catch (\Exception $exception) {
            log_message('error', 'Failed to write dynamic theme CSS', ['error' => $exception->getMessage()]);
        }
    }

    private function getHeroBackground(string $key): ?string
    {
        $hero = $this->get($key);

        if (!is_array($hero)) {
            $hero = $this->defaults[$key] ?? [];
        }

        return $hero['background'] ?? null;
    }

    private function buildCssUrl(?string $filename): ?string
    {
        if ($filename === null || $filename === '' || $filename === '0') {
            return null;
        }

        $url = $filename;
        if (in_array(preg_match('#^https?://#', $filename), [0, false], true)) {
            // Determine if file lives in uploads or default banners
            if (file_exists(UPLOADS_PATH . '/' . ltrim($filename, '/'))) {
                $url = UPLOADS_URL . '/' . ltrim($filename, '/');
            } else {
                $url = BASE_URL . '/Banners-and-portraits/' . ltrim($filename, '/');
            }
        }

        return sprintf("url('%s')", $url);
    }

    private function ensureThemeStyles(): void
    {
        if (! file_exists($this->themeCssPath)) {
            $this->generateThemeStyles();
        }
    }
}
