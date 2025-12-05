<?php
/**
 * Development helper to create a test admin user.
 *
 * Usage:
 *   php scripts/create_dev_admin.php
 */

require_once __DIR__ . '/../config/config.php';

use Delight\Auth\Role;
use GHI\Services\AuthService;
use GHI\Services\DatabaseService;

$email = getenv('DEV_ADMIN_EMAIL') ?: 'admin.dev@example.com';
$password = getenv('DEV_ADMIN_PASSWORD') ?: 'AdminDev!234';
$username = getenv('DEV_ADMIN_NAME') ?: 'Development Admin';

$pdo = DatabaseService::getPdo();
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$existingUser = $stmt->fetch(\PDO::FETCH_ASSOC);

try {
    $auth = AuthService::getInstance();

    if ($existingUser) {
        echo "User with email {$email} already exists. Credentials remain:" . PHP_EOL;
    } else {
        $auth->admin()->createUser($email, $password, $username);
        echo 'Development admin user created successfully.' . PHP_EOL;
    }

    // Ensure admin role is assigned (id may come from query or newly inserted)
    $auth->admin()->addRoleForUserByEmail($email, Role::ADMIN);

    echo 'Credentials:' . PHP_EOL;
    echo "  Email:    {$email}" . PHP_EOL;
    echo "  Password: {$password}" . PHP_EOL;
} catch (\Throwable $e) {
    echo 'Failed to create development admin user: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

