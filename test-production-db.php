<?php
/**
 * Quick Database Connection Test for Production
 * Upload this to your server and access it via browser
 * Delete this file after testing!
 */

echo "<h1>Database Connection Test</h1>";
echo "<hr>";

// Production Database Settings (from environment.php)
$db_config = [
    'host' => 'localhost',
    'name' => 'jhoffkau_GHI',
    'user' => 'jhoffkau_admin',
    'pass' => 'GHI@admin2025',
    'charset' => 'utf8mb4'
];

echo "<h2>Testing Connection...</h2>";
echo "<pre>";
echo "Database Host: " . $db_config['host'] . "\n";
echo "Database Name: " . $db_config['name'] . "\n";
echo "Database User: " . $db_config['user'] . "\n";
echo "Database Pass: " . str_repeat('*', strlen($db_config['pass'])) . "\n";
echo "</pre>";
echo "<hr>";

// Test connection
try {
    $dsn = "mysql:host={$db_config['host']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['user'], $db_config['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ <strong style='color: green;'>SUCCESS: Connected to MySQL server!</strong><br><br>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$db_config['name']}'");
    $db_exists = $stmt->fetch();
    
    if ($db_exists) {
        echo "✅ <strong style='color: green;'>SUCCESS: Database '{$db_config['name']}' exists!</strong><br><br>";
        
        // Connect to the database
        $pdo = new PDO("mysql:host={$db_config['host']};dbname={$db_config['name']};charset={$db_config['charset']}", 
                       $db_config['user'], 
                       $db_config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "✅ <strong style='color: green;'>SUCCESS: Connected to database '{$db_config['name']}'!</strong><br><br>";
        
        // Check tables
        echo "<h3>Tables in database:</h3>";
        echo "<ul>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "<li>⚠️ <strong style='color: orange;'>WARNING: No tables found! Database is empty.</strong></li>";
            echo "<li>You need to import your database SQL file.</li>";
        } else {
            foreach ($tables as $table) {
                echo "<li>✅ $table</li>";
            }
        }
        echo "</ul>";
        
    } else {
        echo "❌ <strong style='color: red;'>ERROR: Database '{$db_config['name']}' does NOT exist!</strong><br><br>";
        echo "<h3>📋 How to Fix:</h3>";
        echo "<ol>";
        echo "<li>Login to cPanel</li>";
        echo "<li>Go to 'MySQL Databases'</li>";
        echo "<li>Create a new database named: <strong>{$db_config['name']}</strong></li>";
        echo "<li>Or check if the database has a different name</li>";
        echo "</ol>";
        
        // Show available databases
        echo "<h3>Available databases:</h3>";
        echo "<ul>";
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($databases as $db) {
            if (!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
                echo "<li>$db</li>";
            }
        }
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>ERROR: Connection failed!</strong><br><br>";
    echo "<strong>Error Message:</strong><br>";
    echo "<pre style='background: #ffebee; padding: 10px; border: 1px solid red;'>";
    echo $e->getMessage();
    echo "</pre>";
    
    echo "<h3>🔧 Common Issues & Solutions:</h3>";
    echo "<ol>";
    echo "<li><strong>Access denied for user:</strong> Wrong username or password</li>";
    echo "<li><strong>Unknown database:</strong> Database doesn't exist yet (create it in cPanel)</li>";
    echo "<li><strong>Can't connect to MySQL server:</strong> MySQL service might be down</li>";
    echo "</ol>";
    
    echo "<h3>📋 How to Fix:</h3>";
    echo "<ol>";
    echo "<li>Login to cPanel</li>";
    echo "<li>Go to 'MySQL Databases'</li>";
    echo "<li>Check/Create database: <strong>{$db_config['name']}</strong></li>";
    echo "<li>Check/Create user: <strong>{$db_config['user']}</strong></li>";
    echo "<li>Make sure user has ALL PRIVILEGES on the database</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<h3>⚠️ Security Warning</h3>";
echo "<p style='color: red;'><strong>DELETE THIS FILE after testing!</strong></p>";
echo "<p>This file contains sensitive information and should not remain on your server.</p>";
?>

