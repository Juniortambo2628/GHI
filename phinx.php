<?php

/**
 * Phinx configuration generated automatically to reuse the database
 * credentials defined in config/environment.php.
 */

$envConfig = require __DIR__ . '/config/environment.php';

$defaultEnvironment = $envConfig['environment'] ?? 'development';

$baseConnection = [
    'adapter' => 'mysql',
    'host' => $envConfig['db_host'] ?? 'localhost',
    'name' => $envConfig['db_name'] ?? 'global_harmony_initiative',
    'user' => $envConfig['db_user'] ?? 'root',
    'pass' => $envConfig['db_pass'] ?? '',
    'port' => (string) ($envConfig['db_port'] ?? 3306),
    'charset' => $envConfig['db_charset'] ?? 'utf8mb4',
];

return [
    'paths' => [
        'migrations' => __DIR__ . '/database/migrations',
        'seeds' => __DIR__ . '/database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => $defaultEnvironment,
        'production' => $baseConnection,
        'development' => $baseConnection,
        'testing' => $baseConnection,
    ],
    'version_order' => 'creation',
];

