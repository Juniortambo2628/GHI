<?php
require_once __DIR__ . '/../config/config.php';

use GHI\Services\DatabaseService;

$table = $argv[1] ?? null;

if (! $table) {
    echo "Usage: php scripts/show_create_table.php <table_name>" . PHP_EOL;
    exit(1);
}

$pdo = DatabaseService::getPdo();
$stmt = $pdo->prepare("SHOW CREATE TABLE `{$table}`");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

print_r($result);

