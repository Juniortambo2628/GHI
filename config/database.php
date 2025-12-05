<?php

/**
 * Database Connection Class
 * Global Harmony Initiative Website
 * 
 * This class now uses Doctrine DBAL but maintains PDO compatibility
 * for backward compatibility with existing code.
 */

use GHI\Services\DatabaseService;

class Database
{
    /**
     * Get database connection instance (Singleton)
     * Returns PDO instance for backward compatibility
     * 
     * @deprecated Consider using DatabaseService::getConnection() for new code
     */
    public static function getInstance(): PDO
    {
        return DatabaseService::getPdo();
    }
}
