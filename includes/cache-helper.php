<?php
/**
 * Simple PHP File-Based Cache Helper
 * For performance optimization without Redis
 */

class SimpleCache {
    private static ?string $cacheDir = null;
    
    private static int $defaultTTL = 3600; // 1 hour
    
    /**
     * Initialize cache directory
     */
    private static function init(): void {
        if (self::$cacheDir === null) {
            self::$cacheDir = BASE_PATH . '/cache/app';
            if (!is_dir(self::$cacheDir)) {
                @mkdir(self::$cacheDir, 0755, true);
            }
        }
    }
    
    /**
     * Generate cache key
     */
    private static function getCacheKey($key): string {
        return self::$cacheDir . '/' . md5((string) $key) . '.cache';
    }
    
    /**
     * Get cached value
     */
    public static function get($key, $default = null) {
        self::init();
        $file = self::getCacheKey($key);
        
        if (!file_exists($file)) {
            return $default;
        }
        
        $data = @file_get_contents($file);
        if ($data === false) {
            return $default;
        }
        
        $cached = @unserialize($data);
        if ($cached === false) {
            return $default;
        }
        
        // Check if expired
        if (isset($cached['expires']) && $cached['expires'] < time()) {
            @unlink($file);
            return $default;
        }
        
        return $cached['value'] ?? $default;
    }
    
    /**
     * Set cache value
     */
    public static function set($key, $value, $ttl = null): bool {
        self::init();
        $file = self::getCacheKey($key);
        $ttl ??= self::$defaultTTL;
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        return @file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }
    
    /**
     * Delete cache value
     */
    public static function delete($key) {
        self::init();
        $file = self::getCacheKey($key);
        return @unlink($file);
    }
    
    /**
     * Remember - Get from cache or execute callback
     */
    public static function remember($key, $callback, $ttl = null) {
        $value = self::get($key);
        
        if ($value === null) {
            $value = $callback();
            self::set($key, $value, $ttl);
        }
        
        return $value;
    }
    
    /**
     * Clear all cache
     */
    public static function clear(): bool {
        self::init();
        $files = glob(self::$cacheDir . '/*.cache');
        foreach ($files as $file) {
            @unlink($file);
        }
        
        return true;
    }
}

