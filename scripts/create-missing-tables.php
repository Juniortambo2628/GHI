<?php

/**
 * Create Missing Database Tables
 * Creates stories and newsletter_subscribers tables
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();

echo "Creating missing database tables...\n\n";

// Create stories table
echo "Creating 'stories' table...\n";

try {
    $sql = "CREATE TABLE IF NOT EXISTS `stories` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(191) NOT NULL UNIQUE,
        `description` TEXT,
        `content` LONGTEXT,
        `image` VARCHAR(255) NULL,
        `category` ENUM('education', 'health', 'livelihood', 'empowerment', 'partnerships') NOT NULL DEFAULT 'education',
        `likes` INT DEFAULT 0,
        `comments` INT DEFAULT 0,
        `status` ENUM('draft', 'published') DEFAULT 'published',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_slug` (`slug`),
        INDEX `idx_status` (`status`),
        INDEX `idx_category` (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "  ✓ 'stories' table created successfully\n\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "  ℹ 'stories' table already exists\n\n";
    } else {
        echo "  ✗ Error creating 'stories' table: " . $e->getMessage() . "\n\n";
    }
}

// Create newsletter_subscribers table
echo "Creating 'newsletter_subscribers' table...\n";

try {
    $sql = "CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(191) NOT NULL UNIQUE,
        `name` VARCHAR(255) NULL,
        `status` ENUM('active', 'unsubscribed') DEFAULT 'active',
        `subscribed_at` DATETIME NULL,
        `last_email_sent` DATETIME NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_email` (`email`),
        INDEX `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "  ✓ 'newsletter_subscribers' table created successfully\n\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "  ℹ 'newsletter_subscribers' table already exists\n\n";
    } else {
        echo "  ✗ Error creating 'newsletter_subscribers' table: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ Table creation completed!\n";
