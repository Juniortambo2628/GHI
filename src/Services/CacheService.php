<?php

/**
 * Cache Service using Symfony Cache
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Psr\Cache\CacheItemInterface;

class CacheService
{
    private static ?AdapterInterface $instance = null;

    private static string $defaultNamespace = 'ghi';

    private static int $defaultLifetime = 3600; // 1 hour

    /**
     * Get cache adapter instance (Singleton)
     */
    public static function getInstance(): AdapterInterface
    {
        if (!self::$instance instanceof \Symfony\Component\Cache\Adapter\AdapterInterface) {
            self::$instance = self::createCache();
        }

        return self::$instance;
    }

    /**
     * Create and configure cache adapter
     */
    private static function createCache(): AdapterInterface
    {
        $cachePath = defined('CACHE_PATH') ? CACHE_PATH . '/app' : __DIR__ . '/../../cache/app';

        // Ensure cache directory exists
        if (! is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }

        return new FilesystemAdapter(
            self::$defaultNamespace,
            self::$defaultLifetime,
            $cachePath
        );
    }

    /**
     * Get item from cache
     */
    public static function get(string $key, callable $callback = null, int $lifetime = null): mixed
    {
        $cache = self::getInstance();
        $item = $cache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        // If callback provided, execute and cache result
        if ($callback !== null) {
            $value = $callback();
            self::set($key, $value, $lifetime);
            return $value;
        }

        return null;
    }

    /**
     * Set item in cache
     */
    public static function set(string $key, mixed $value, int $lifetime = null): bool
    {
        try {
            $cache = self::getInstance();
            $item = $cache->getItem($key);
            $item->set($value);

            if ($lifetime !== null) {
                $item->expiresAfter($lifetime);
            } else {
                $item->expiresAfter(self::$defaultLifetime);
            }

            return $cache->save($item);
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Cache set failed', [
                    'key' => $key,
                    'error' => $exception->getMessage(),
                ]);
            }

            return false;
        }
    }

    /**
     * Delete item from cache
     */
    public static function delete(string $key): bool
    {
        try {
            $cache = self::getInstance();
            return $cache->deleteItem($key);
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Cache delete failed', [
                    'key' => $key,
                    'error' => $exception->getMessage(),
                ]);
            }

            return false;
        }
    }

    /**
     * Check if item exists in cache
     */
    public static function has(string $key): bool
    {
        $cache = self::getInstance();
        return $cache->hasItem($key);
    }

    /**
     * Clear all cache
     */
    public static function clear(): bool
    {
        try {
            $cache = self::getInstance();
            return $cache->clear();
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Cache clear failed', ['error' => $exception->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Clear cache by namespace/prefix
     */
    public static function clearByPrefix(string $prefix): bool
    {
        try {
            $cache = self::getInstance();
            $items = $cache->getItems([$prefix . '*']);

            foreach ($items as $item) {
                $cache->deleteItem($item->getKey());
            }

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Cache clear by prefix failed', [
                    'prefix' => $prefix,
                    'error' => $exception->getMessage(),
                ]);
            }

            return false;
        }
    }

    /**
     * Remember - Get from cache or execute callback and cache result
     */
    public static function remember(string $key, callable $callback, int $lifetime = null): mixed
    {
        return self::get($key, $callback, $lifetime);
    }

    /**
     * Forget - Delete item from cache
     */
    public static function forget(string $key): bool
    {
        return self::delete($key);
    }
}
