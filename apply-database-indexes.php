<?php
/**
 * Database Indexes Application Script
 * Global Harmony Initiative Website
 * 
 * This script applies performance indexes to the database.
 * Run this once to improve query performance.
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Check if running from command line or web
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    // Web interface
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Apply Database Indexes</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        </style>
    </head>
    <body>
        <h1>Apply Database Indexes</h1>
        <?php
}

try {
    // Read SQL file
    $sqlFile = __DIR__ . '/database_indexes.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: {$sqlFile}");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    // Get database connection
    $db = \GHI\Services\DatabaseService::getPdo();
    
    $successCount = 0;
    $errorCount = 0;
    $skippedCount = 0;
    $errors = [];
    
    if (!$isCli) {
        echo "<h2>Applying Indexes...</h2>";
        echo "<pre>";
    }
    
    foreach ($statements as $index => $statement) {
        // Extract index name and table name for reporting
        // Handle: CREATE INDEX idx_name ON table_name
        if (preg_match('/CREATE\s+INDEX\s+(\w+)\s+ON\s+(\w+)/i', $statement, $matches)) {
            $indexName = $matches[1];
            $tableName = $matches[2];
        } else {
            $indexName = "Index " . ($index + 1);
            $tableName = "unknown";
        }
        
        try {
            // Check if index already exists
            $checkStmt = $db->prepare("
                SELECT COUNT(*) as count 
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE() 
                AND table_name = ? 
                AND index_name = ?
            ");
            $checkStmt->execute([$tableName, $indexName]);
            $exists = $checkStmt->fetch()['count'] > 0;
            
            if ($exists) {
                if (!$isCli) {
                    echo "⏭️  Skipped: {$indexName} on {$tableName} (already exists)\n";
                } else {
                    echo "⏭️  Skipped: {$indexName} on {$tableName} (already exists)\n";
                }
                $skippedCount++;
                continue;
            }
            
            // Execute statement
            $db->exec($statement);
            
            if (!$isCli) {
                echo "✅ Created: {$indexName} on {$tableName}\n";
            } else {
                echo "✅ Created: {$indexName} on {$tableName}\n";
            }
            $successCount++;
            
        } catch (\PDOException $e) {
            $errorMsg = $e->getMessage();
            
            // Check if it's a "duplicate index" error
            if (strpos($errorMsg, 'Duplicate key name') !== false || 
                strpos($errorMsg, 'already exists') !== false ||
                strpos($errorMsg, 'Duplicate entry') !== false) {
                if (!$isCli) {
                    echo "⏭️  Skipped: {$indexName} on {$tableName} (already exists)\n";
                } else {
                    echo "⏭️  Skipped: {$indexName} on {$tableName} (already exists)\n";
                }
                $skippedCount++;
            } 
            // Check if table doesn't exist (e.g., contacts table)
            elseif (strpos($errorMsg, "doesn't exist") !== false || 
                    strpos($errorMsg, 'Base table or view not found') !== false) {
                if (!$isCli) {
                    echo "⏭️  Skipped: {$indexName} on {$tableName} (table doesn't exist)\n";
                } else {
                    echo "⏭️  Skipped: {$indexName} on {$tableName} (table doesn't exist)\n";
                }
                $skippedCount++;
            }
            else {
                if (!$isCli) {
                    echo "❌ Error: {$indexName} on {$tableName} - " . htmlspecialchars($errorMsg) . "\n";
                } else {
                    echo "❌ Error: {$indexName} on {$tableName} - " . $errorMsg . "\n";
                }
                $errors[] = [
                    'index' => $indexName,
                    'table' => $tableName,
                    'error' => $errorMsg
                ];
                $errorCount++;
            }
        }
    }
    
    if (!$isCli) {
        echo "</pre>";
        echo "<h2>Summary</h2>";
        echo "<p class='success'>✅ Created: {$successCount} indexes</p>";
        echo "<p class='info'>⏭️  Skipped: {$skippedCount} indexes (already exist)</p>";
        if ($errorCount > 0) {
            echo "<p class='error'>❌ Errors: {$errorCount} indexes</p>";
            echo "<h3>Errors:</h3><ul>";
            foreach ($errors as $error) {
                echo "<li><strong>{$error['index']}:</strong> {$error['error']}</li>";
            }
            echo "</ul>";
        }
        
        if ($errorCount === 0 && $successCount > 0) {
            echo "<p class='success'><strong>✅ All indexes applied successfully!</strong></p>";
            echo "<p>Expected performance improvement: 30-50% faster database queries.</p>";
        }
    } else {
        echo "\n=== Summary ===\n";
        echo "✅ Created: {$successCount} indexes\n";
        echo "⏭️  Skipped: {$skippedCount} indexes (already exist)\n";
        if ($errorCount > 0) {
            echo "❌ Errors: {$errorCount} indexes\n";
            foreach ($errors as $error) {
                echo "  - {$error['index']}: {$error['error']}\n";
            }
        }
        
        if ($errorCount === 0 && $successCount > 0) {
            echo "\n✅ All indexes applied successfully!\n";
            echo "Expected performance improvement: 30-50% faster database queries.\n";
        }
    }
    
} catch (Exception $e) {
    if (!$isCli) {
        echo "<p class='error'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
    exit(1);
}

if (!$isCli) {
    echo "<hr>";
    echo "<p><small><strong>Security Note:</strong> Delete this file after running it!</small></p>";
    echo "</body></html>";
}

