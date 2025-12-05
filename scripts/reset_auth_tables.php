<?php

/**
 * Recreate Delight Auth tables for development.
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\DatabaseService;

$pdo = DatabaseService::getPdo();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {

    // Backup existing users table if schema differs
    $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->fetchColumn();
    if ($tableExists) {
        $backupName = 'users_backup_' . date('Ymd_His');
        $pdo->exec("RENAME TABLE `users` TO `{$backupName}`");
        echo "Existing users table renamed to {$backupName}." . PHP_EOL;
    }

    $tablesToDrop = [
        'users_confirmations',
        'users_remembered',
        'users_resets',
        'users_throttling',
    ];

    foreach ($tablesToDrop as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
    }

    $pdo->exec(
        "CREATE TABLE `users` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(249) NOT NULL,
            `password` varchar(255) NOT NULL,
            `username` varchar(100) DEFAULT NULL,
            `status` tinyint unsigned NOT NULL DEFAULT 0,
            `verified` tinyint unsigned NOT NULL DEFAULT 0,
            `resettable` tinyint unsigned NOT NULL DEFAULT 1,
            `roles_mask` int unsigned NOT NULL DEFAULT 0,
            `registered` int unsigned NOT NULL,
            `last_login` int unsigned DEFAULT NULL,
            `force_logout` smallint unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email_unique` (`email`),
            UNIQUE KEY `username_unique` (`username`),
            KEY `status_index` (`status`),
            KEY `verified_index` (`verified`),
            KEY `resettable_index` (`resettable`),
            KEY `roles_mask_index` (`roles_mask`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE `users_confirmations` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int unsigned NOT NULL,
            `selector` varchar(16) NOT NULL,
            `token` varchar(255) NOT NULL,
            `expires` int unsigned NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `selector_unique` (`selector`),
            KEY `user_id_index` (`user_id`),
            CONSTRAINT `users_confirmations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE `users_remembered` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `user` int unsigned NOT NULL,
            `selector` varchar(20) NOT NULL,
            `token` varchar(255) NOT NULL,
            `expires` int unsigned NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `selector_unique` (`selector`),
            KEY `user_index` (`user`),
            CONSTRAINT `users_remembered_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE `users_resets` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `user_id` int unsigned NOT NULL,
            `selector` varchar(20) NOT NULL,
            `token` varchar(255) NOT NULL,
            `expires` int unsigned NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `selector_unique` (`selector`),
            KEY `user_id_index` (`user_id`),
            CONSTRAINT `users_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE `users_throttling` (
            `bucket` varchar(44) NOT NULL,
            `tokens` float NOT NULL,
            `replenished` int unsigned NOT NULL,
            `expires` int unsigned NOT NULL,
            PRIMARY KEY (`bucket`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    echo 'Auth tables recreated using Delight schema.' . PHP_EOL;
} catch (\Throwable $e) {
    echo 'Failed to reset auth tables: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

