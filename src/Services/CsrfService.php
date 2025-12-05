<?php

/**
 * CSRF Protection Service
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenStorage\NativeSessionTokenStorage;

class CsrfService
{
    private static ?CsrfTokenManager $instance = null;

    private static string $tokenName;

    /**
     * Get CSRF token manager instance (Singleton)
     */
    public static function getInstance(): CsrfTokenManager
    {
        if (!self::$instance instanceof \Symfony\Component\Security\Csrf\CsrfTokenManager) {
            self::$instance = self::createTokenManager();
            self::$tokenName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : '_token';
        }

        return self::$instance;
    }

    /**
     * Create and configure token manager
     */
    private static function createTokenManager(): CsrfTokenManager
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Use native session-based token storage (doesn't require RequestStack)
        $tokenStorage = new NativeSessionTokenStorage();
        $tokenGenerator = new \Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator();

        return new CsrfTokenManager($tokenGenerator, $tokenStorage);
    }

    /**
     * Generate a CSRF token for a given token ID
     */
    public static function generateToken(string $tokenId = 'form'): string
    {
        return self::getInstance()->getToken($tokenId)->getValue();
    }

    /**
     * Validate a CSRF token
     */
    public static function validateToken(string $token, string $tokenId = 'form'): bool
    {
        try {
            return self::getInstance()->isTokenValid(
                new \Symfony\Component\Security\Csrf\CsrfToken($tokenId, $token)
            );
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get token name for form fields
     */
    public static function getTokenName(): string
    {
        if (! isset(self::$tokenName)) {
            self::getInstance(); // Initialize if needed
        }

        return self::$tokenName;
    }

    /**
     * Generate hidden input field HTML
     */
    public static function getTokenField(string $tokenId = 'form'): string
    {
        $tokenName = self::getTokenName();
        $token = self::generateToken($tokenId);

        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            htmlspecialchars($tokenName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}
