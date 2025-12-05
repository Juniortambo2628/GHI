<?php

/**
 * Authentication Service using Delight Auth
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Delight\Auth\Auth;
use Delight\Db\PdoDatabase;
use Delight\Db\PdoDataSource;
use GHI\Services\DatabaseService;
use GHI\Services\MailService;

class AuthService
{
    private static ?Auth $instance = null;

    private static ?PdoDatabase $db = null;

    /**
     * Get Auth instance (Singleton)
     */
    public static function getInstance(): Auth
    {
        if (!self::$instance instanceof \Delight\Auth\Auth) {
            self::$instance = self::createAuth();
        }

        return self::$instance;
    }

    /**
     * Create and configure Auth instance
     */
    private static function createAuth(): Auth
    {
        // Get PDO connection from DatabaseService
        $pdo = DatabaseService::getPdo();

        // Create Delight Auth database wrapper
        self::$db = PdoDatabase::fromPdo($pdo);

        // Create Auth instance
        $auth = new Auth(self::$db, null, null, false);

        return $auth;
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        try {
            return self::getInstance()->isLoggedIn();
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Auth check failed', ['error' => $exception->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Get current user ID
     */
    public static function getUserId(): ?int
    {
        try {
            if (self::isLoggedIn()) {
                return self::getInstance()->getUserId();
            }
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Get user ID failed', ['error' => $exception->getMessage()]);
            }
        }

        return null;
    }

    /**
     * Get current user email
     */
    public static function getEmail(): ?string
    {
        try {
            if (self::isLoggedIn()) {
                return self::getInstance()->getEmail();
            }
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Get email failed', ['error' => $exception->getMessage()]);
            }
        }

        return null;
    }

    /**
     * Login user
     */
    public static function login(string $email, string $password, int $rememberDuration = null): bool
    {
        try {
            self::getInstance()->login($email, $password, $rememberDuration);
            $userId = self::getInstance()->getUserId();

            if (function_exists('log_message')) {
                log_message('info', 'User logged in', ['email' => $email, 'user_id' => $userId]);
            }

            // Dispatch login event
            if (class_exists(\GHI\Events\UserLoggedInEvent::class)) {
                $event = new \GHI\Events\UserLoggedInEvent($userId, $email);
                if (function_exists('event_dispatch')) {
                    event_dispatch($event, \GHI\Events\UserLoggedInEvent::NAME);
                }
            }

            return true;
        } catch (\Delight\Auth\InvalidEmailException) {
            if (function_exists('log_message')) {
                log_message('warning', 'Login failed: Invalid email', ['email' => $email]);
            }

            return false;
        } catch (\Delight\Auth\InvalidPasswordException) {
            if (function_exists('log_message')) {
                log_message('warning', 'Login failed: Invalid password', ['email' => $email]);
            }

            return false;
        } catch (\Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Login failed', ['email' => $email, 'error' => $e->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Logout user
     */
    public static function logout(): bool
    {
        try {
            $email = self::getEmail();
            self::getInstance()->logOut();

            if (function_exists('log_message')) {
                log_message('info', 'User logged out', ['email' => $email]);
            }

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Logout failed', ['error' => $exception->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Register new user
     */
    public static function register(string $email, string $password, ?string $username = null): int
    {
        try {
            $userId = self::getInstance()->register($email, $password, $username);

            if (function_exists('log_message')) {
                log_message('info', 'User registered', ['email' => $email, 'user_id' => $userId]);
            }

            return $userId;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Registration failed', ['email' => $email, 'error' => $exception->getMessage()]);
            }

            throw $exception;
        }
    }

    /**
     * Change password
     */
    public static function changePassword(string $oldPassword, string $newPassword): bool
    {
        try {
            self::getInstance()->changePassword($oldPassword, $newPassword);

            if (function_exists('log_message')) {
                log_message('info', 'Password changed', ['user_id' => self::getUserId()]);
            }

            return true;
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('error', 'Password change failed', ['error' => $exception->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Require login - redirect if not logged in
     */
    public static function requireLogin(?string $redirectUrl = null): void
    {
        if (! self::isLoggedIn()) {
            $redirectUrl ??= BASE_URL . '/admin/login.php';
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    /**
     * Request password reset email
     */
    public static function requestPasswordReset(string $email): bool
    {
        try {
            self::getInstance()->forgotPassword($email, function ($selector, $token) use ($email): void {
                $resetUrl = rtrim(BASE_URL, '/') . '/admin/reset-password.php?selector=' . urlencode($selector) . '&token=' . urlencode($token);

                MailService::sendPasswordReset($email, $token, $resetUrl);

                if (function_exists('log_message')) {
                    log_message('info', 'Password reset link sent', ['email' => $email]);
                }
            });

            return true;
        } catch (\Delight\Auth\InvalidEmailException | \Delight\Auth\EmailNotVerifiedException) {
            // For security, do not reveal whether the email exists
            if (function_exists('log_message')) {
                log_message('warning', 'Password reset request for non-existent or unverified email', ['email' => $email]);
            }

            return true;
        } catch (\Delight\Auth\ResetDisabledException $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Password reset disabled', ['error' => $e->getMessage()]);
            }

            return false;
        } catch (\Delight\Auth\TooManyRequestsException) {
            if (function_exists('log_message')) {
                log_message('warning', 'Password reset rate limited', ['email' => $email]);
            }

            return false;
        } catch (\Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Password reset request failed', ['email' => $email, 'error' => $e->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Verify whether a password reset token is valid
     */
    public static function canResetPassword(string $selector, string $token): bool
    {
        try {
            return self::getInstance()->canResetPassword($selector, $token);
        } catch (\Exception $exception) {
            if (function_exists('log_message')) {
                log_message('warning', 'Password reset token invalid', [
                    'selector' => $selector,
                    'error' => $exception->getMessage(),
                ]);
            }

            return false;
        }
    }

    /**
     * Reset password using selector/token pair
     */
    public static function resetPassword(string $selector, string $token, string $newPassword): bool
    {
        try {
            self::getInstance()->resetPassword($selector, $token, $newPassword);

            if (function_exists('log_message')) {
                log_message('info', 'Password reset successfully', ['selector' => $selector]);
            }

            return true;
        } catch (\Delight\Auth\InvalidSelectorTokenPairException) {
            if (function_exists('log_message')) {
                log_message('warning', 'Invalid selector/token pair during password reset', ['selector' => $selector]);
            }

            return false;
        } catch (\Delight\Auth\TokenExpiredException) {
            if (function_exists('log_message')) {
                log_message('warning', 'Password reset token expired', ['selector' => $selector]);
            }

            return false;
        } catch (\Delight\Auth\ResetDisabledException $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Password reset disabled', ['error' => $e->getMessage()]);
            }

            return false;
        } catch (\Delight\Auth\InvalidPasswordException) {
            if (function_exists('log_message')) {
                log_message('warning', 'New password did not meet requirements', ['selector' => $selector]);
            }

            return false;
        } catch (\Delight\Auth\TooManyRequestsException) {
            if (function_exists('log_message')) {
                log_message('warning', 'Password reset rate limited during finalize', ['selector' => $selector]);
            }

            return false;
        } catch (\Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Password reset failed', ['selector' => $selector, 'error' => $e->getMessage()]);
            }

            return false;
        }
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}
