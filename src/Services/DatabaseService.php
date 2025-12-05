<?php

/**
 * Database Service using Doctrine DBAL
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PDO;

class DatabaseService
{
    private static ?Connection $instance = null;

    private static ?PDO $pdoInstance = null;

    /**
     * Get Doctrine DBAL connection instance (Singleton)
     */
    public static function getConnection(): Connection
    {
        if (!self::$instance instanceof \Doctrine\DBAL\Connection) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Get PDO instance for backward compatibility
     */
    public static function getPdo(): PDO
    {
        if (!self::$pdoInstance instanceof \PDO) {
            $connection = self::getConnection();
            self::$pdoInstance = $connection->getNativeConnection();
        }

        return self::$pdoInstance;
    }

    /**
     * Create and configure DBAL connection
     */
    private static function createConnection(): Connection
    {
        try {
            $connectionParams = [
                'dbname' => DB_NAME,
                'user' => DB_USER,
                'password' => DB_PASS,
                'host' => DB_HOST,
                'driver' => 'pdo_mysql',
                'charset' => DB_CHARSET,
                'driverOptions' => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            ];

            $connection = DriverManager::getConnection($connectionParams);

            // Test connection
            $connection->connect();

            // Log successful connection
            if (function_exists('log_message')) {
                log_message('info', 'Database connection established', [
                    'host' => DB_HOST,
                    'database' => DB_NAME,
                ]);
            }

            return $connection;
        } catch (Exception $exception) {
            // Log error
            if (function_exists('log_message')) {
                log_message('error', 'Database connection failed', [
                    'error' => $exception->getMessage(),
                    'host' => DB_HOST,
                    'database' => DB_NAME,
                ]);
            }

            if (ENVIRONMENT === 'development') {
                die('Database Connection Error: ' . $exception->getMessage());
            }

            die('Database connection failed. Please contact the administrator.');
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
