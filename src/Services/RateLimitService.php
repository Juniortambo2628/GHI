<?php

/**
 * Rate Limiting Service using Symfony Rate Limiter
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\Policy\TokenBucketLimiter;

class RateLimitService
{
    private static array $factories = [];

    private static InMemoryStorage $storage;

    /**
     * Get rate limiter factory for a specific policy
     *
     * @param string $policy Policy name (e.g., 'api', 'login', 'upload')
     * @param array $config Rate limit configuration
     */
    private static function getFactory(string $policy, array $config): RateLimiterFactory
    {
        if (!isset(self::$factories[$policy])) {
            if (!isset(self::$storage)) {
                self::$storage = new InMemoryStorage();
            }

            self::$factories[$policy] = new RateLimiterFactory([
                'id' => $policy,
                'policy' => 'token_bucket',
                'limit' => $config['limit'] ?? 100,
                'rate' => [
                    'interval' => $config['interval'] ?? '1 minute',
                    'amount' => $config['amount'] ?? 100,
                ],
            ], self::$storage);
        }

        return self::$factories[$policy];
    }

    /**
     * Check if request is allowed
     *
     * @param string $key Unique identifier (e.g., IP address, user ID)
     * @param string $policy Policy name (default: 'api')
     * @param array $config Custom configuration (optional)
     * @return bool True if allowed, false if rate limited
     */
    public static function isAllowed(string $key, string $policy = 'api', array $config = []): bool
    {
        $defaultConfig = [
        'limit' => 100,
        'interval' => '1 minute',
        'amount' => 100,
        ];

        $config = array_merge($defaultConfig, $config);
        $limiter = self::getFactory($policy, $config)->create($key);
        $limit = $limiter->consume();

        return $limit->isAccepted();
    }

    /**
     * Get remaining tokens
     *
     * @param string $key Unique identifier
     * @param string $policy Policy name (default: 'api')
     * @param array $config Custom configuration (optional)
     * @return int Remaining tokens
     */
    public static function getRemaining(string $key, string $policy = 'api', array $config = []): int
    {
        $defaultConfig = [
        'limit' => 100,
        'interval' => '1 minute',
        'amount' => 100,
        ];

        $config = array_merge($defaultConfig, $config);
        $limiter = self::getFactory($policy, $config)->create($key);
        $limit = $limiter->consume();

        return $limit->getRemainingTokens();
    }

    /**
     * Get client identifier (IP address or user ID)
     *
     * @return string Client identifier
     */
    public static function getClientKey(): string
    {
        // Try to get user ID from session
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['admin_user_id'])) {
            return 'user_' . $_SESSION['admin_user_id'];
        }

        // Fall back to IP address
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Handle proxy headers
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        }

        return 'ip_' . $ip;
    }

    /**
     * Check rate limit and send 429 response if exceeded
     *
     * @param string $policy Policy name
     * @param array $config Custom configuration
     * @return bool True if allowed, false if rate limited (response sent)
     */
    public static function checkAndRespond(string $policy = 'api', array $config = []): bool
    {
        $key = self::getClientKey();

        if (!self::isAllowed($key, $policy, $config)) {
            http_response_code(429);
            header('Content-Type: application/json');
            header('Retry-After: 60');

            echo json_encode([
            'error' => 'Rate limit exceeded',
            'message' => 'Too many requests. Please try again later.',
            'retry_after' => 60,
            ]);

            return false;
        }

        return true;
    }
}
