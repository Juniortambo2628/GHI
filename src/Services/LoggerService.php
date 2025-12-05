<?php

/**
 * Logger Service
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class LoggerService
{
    private static ?Logger $instance = null;

    /**
     * Get logger instance (Singleton)
     */
    public static function getInstance(): Logger
    {
        if (!self::$instance instanceof \Monolog\Logger) {
            self::$instance = self::createLogger();
        }

        return self::$instance;
    }

    /**
     * Create and configure logger
     */
    private static function createLogger(): Logger
    {
        $logger = new Logger('ghi');

        // Determine log level
        $logLevel = self::getLogLevel();
        $logPath = defined('LOG_PATH') ? LOG_PATH : BASE_PATH . '/logs/app.log';

        // Ensure log directory exists
        $logDir = dirname((string) $logPath);
        if (! is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Rotating file handler (keeps 30 days of logs)
        $fileHandler = new RotatingFileHandler($logPath, 30, $logLevel);
        $fileFormatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        );
        $fileHandler->setFormatter($fileFormatter);
        $logger->pushHandler($fileHandler);

        // Console handler for development
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $consoleHandler = new StreamHandler('php://stdout', $logLevel);
            $consoleFormatter = new LineFormatter(
                "%datetime% [%level_name%]: %message% %context%\n",
                'Y-m-d H:i:s'
            );
            $consoleHandler->setFormatter($consoleFormatter);
            $logger->pushHandler($consoleHandler);
        }

        return $logger;
    }

    /**
     * Get log level from configuration
     */
    private static function getLogLevel(): int
    {
        $level = defined('LOG_LEVEL') ? LOG_LEVEL : 'debug';

        return match (strtolower((string) $level)) {
            'debug' => Logger::DEBUG,
            'info' => Logger::INFO,
            'notice' => Logger::NOTICE,
            'warning' => Logger::WARNING,
            'error' => Logger::ERROR,
            'critical' => Logger::CRITICAL,
            'alert' => Logger::ALERT,
            'emergency' => Logger::EMERGENCY,
            default => Logger::DEBUG,
        };
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}
