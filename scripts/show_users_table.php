<?php
require_once __DIR__ . '/../config/config.php';

use GHI\Services\DatabaseService;

$pdo = DatabaseService::getPdo();
$statement = $pdo->query('SHOW TABLES');
$tables = $statement ? $statement->fetchAll(\PDO::FETCH_COLUMN) : [];

echo 'Tables:' . PHP_EOL;
foreach ($tables as $table) {
    echo ' - ' . $table . PHP_EOL;
}

echo PHP_EOL . 'Columns for users table:' . PHP_EOL;

$statement = $pdo->query('SHOW COLUMNS FROM users');
$columns = $statement ? $statement->fetchAll(\PDO::FETCH_ASSOC) : [];

foreach ($columns as $column) {
    echo $column['Field'] . ' - ' . $column['Type'] . PHP_EOL;
}

