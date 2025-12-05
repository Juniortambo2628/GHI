<?php
/**
 * Simple SQL Migration Runner
 * Run this file directly in the browser while logged into admin
 */

require_once __DIR__ . '/../config/config.php';

// Check authentication
require_login();

// Get connection from global scope
global $conn;

if (!$conn) {
    die("❌ Database connection not available");
}

try {
    // Read SQL file
    $sqlFile = __DIR__ . '/migrations/create_donations_table.sql';
    $sql = file_get_contents($sqlFile);

    if ($sql === false) {
        throw new Exception("Failed to read SQL file: {$sqlFile}");
    }

    // Execute SQL
    $conn->executeStatement($sql);

    echo "<h2>✅ Donations table created successfully!</h2>";

    // Verify table exists
    $schemaManager = $conn->createSchemaManager();
    $tables = $schemaManager->listTableNames();

    if (in_array('donations', $tables)) {
        echo "<p>✅ Verified: donations table exists in database</p>";
        
        // Show table structure
        $columns = $schemaManager->listTableColumns('donations');
        echo "<h3>Table Structure:</h3><ul>";
        foreach ($columns as $column) {
            echo "<li><strong>{$column->getName()}</strong>: {$column->getType()->getName()}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>⚠️ Warning: donations table not found after migration</p>";
    }

} catch (Exception $e) {
    echo "<h2>❌ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>

<p><a href="<?php echo BASE_URL; ?>/admin/donations.php">Go to Donations Dashboard</a></p>
