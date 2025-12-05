<?php
/**
 * Database Structure Test
 * Tests for misalignment and consistency issues
 */

require_once __DIR__ . '/../config/config.php';

use GHI\Services\DatabaseService;

// Get database connection
$db = DatabaseService::getConnection();

echo "=== Database Structure Test ===\n\n";

// List of tables to check
$tables = [
    'causes',
    'initiatives',
    'events',
    'stories',
    'impact_activities',
    'newsletter_subscribers',
    'contact_submissions',
];

foreach ($tables as $tableName) {
    echo "Checking table: $tableName\n";
    echo str_repeat('-', 50) . "\n";
    
    try {
        // Get table schema
        $schemaManager = $db->createSchemaManager();
        
        // Check if table exists
        if (!$schemaManager->tablesExist([$tableName])) {
            echo "❌ Table does not exist!\n\n";
            continue;
        }
        
        echo "✅ Table exists\n";
        
        // Get columns
        $columns = $schemaManager->listTableColumns($tableName);
        
        echo "Columns:\n";
        foreach ($columns as $column) {
            $name = $column->getName();
            $type = $column->getType()->getName();
            $nullable = $column->getNotnull() ? 'NOT NULL' : 'NULL';
            $default = $column->getDefault() !== null ? "DEFAULT: {$column->getDefault()}" : '';
            
            echo "  - $name ($type) $nullable $default\n";
        }
        
        // Get row count
        $qb = $db->createQueryBuilder();
        $count = $qb->select('COUNT(*) as count')
            ->from($tableName)
            ->executeQuery()
            ->fetchOne();
        
        echo "Row count: $count\n";
        
        // Check for common issues
        $issues = [];
        
        // Check for ID column
        if (!isset($columns['id'])) {
            $issues[] = "Missing 'id' column";
        }
        
        // Check for created_at/updated_at
        if (!isset($columns['created_at'])) {
            $issues[] = "Missing 'created_at' column";
        }
        
        // Check for status column where applicable
        if (in_array($tableName, ['causes', 'initiatives', 'events', 'stories', 'impact_activities'])) {
            if (!isset($columns['status'])) {
                $issues[] = "Missing 'status' column";
            }
        }
        
        // Check for title/name column where applicable
        if (in_array($tableName, ['causes', 'initiatives', 'events', 'stories', 'impact_activities'])) {
            if (!isset($columns['title'])) {
                $issues[] = "Missing 'title' column";
            }
        }
        
        // Check for description column where applicable
        if (in_array($tableName, ['causes', 'initiatives', 'events', 'stories', 'impact_activities'])) {
            if (!isset($columns['description'])) {
                $issues[] = "Missing 'description' column";
            }
        }
        
        if (empty($issues)) {
            echo "✅ No structural issues found\n";
        } else {
            echo "⚠️ Issues found:\n";
            foreach ($issues as $issue) {
                echo "  - $issue\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

// Test for data consistency
echo "=== Data Consistency Tests ===\n\n";

try {
    // Check for orphaned records (foreign key consistency)
    
    // 1. Check initiatives with invalid cause_id
    echo "Checking initiatives with invalid cause_id...\n";
    $qb = $db->createQueryBuilder();
    $orphanedInitiatives = $qb->select('i.id', 'i.title', 'i.cause_id')
        ->from('initiatives', 'i')
        ->leftJoin('i', 'causes', 'c', 'i.cause_id = c.id')
        ->where('i.cause_id IS NOT NULL')
        ->andWhere('c.id IS NULL')
        ->executeQuery()
        ->fetchAllAssociative();
    
    if (empty($orphanedInitiatives)) {
        echo "✅ No orphaned initiatives found\n";
    } else {
        echo "⚠️ Found " . count($orphanedInitiatives) . " initiatives with invalid cause_id:\n";
        foreach ($orphanedInitiatives as $record) {
            echo "  - ID: {$record['id']}, Title: {$record['title']}, Invalid Cause ID: {$record['cause_id']}\n";
        }
    }
    echo "\n";
    
    // 2. Check events with invalid initiative_id
    echo "Checking events with invalid initiative_id...\n";
    $qb = $db->createQueryBuilder();
    $orphanedEvents = $qb->select('e.id', 'e.title', 'e.initiative_id')
        ->from('events', 'e')
        ->leftJoin('e', 'initiatives', 'i', 'e.initiative_id = i.id')
        ->where('e.initiative_id IS NOT NULL')
        ->andWhere('i.id IS NULL')
        ->executeQuery()
        ->fetchAllAssociative();
    
    if (empty($orphanedEvents)) {
        echo "✅ No orphaned events found\n";
    } else {
        echo "⚠️ Found " . count($orphanedEvents) . " events with invalid initiative_id:\n";
        foreach ($orphanedEvents as $record) {
            echo "  - ID: {$record['id']}, Title: {$record['title']}, Invalid Initiative ID: {$record['initiative_id']}\n";
        }
    }
    echo "\n";
    
    // 3. Check impact_activities with invalid event_id
    echo "Checking impact_activities with invalid event_id...\n";
    $qb = $db->createQueryBuilder();
    $orphanedImpacts = $qb->select('ia.id', 'ia.title', 'ia.event_id')
        ->from('impact_activities', 'ia')
        ->leftJoin('ia', 'events', 'e', 'ia.event_id = e.id')
        ->where('ia.event_id IS NOT NULL')
        ->andWhere('e.id IS NULL')
        ->executeQuery()
        ->fetchAllAssociative();
    
    if (empty($orphanedImpacts)) {
        echo "✅ No orphaned impact activities found\n";
    } else {
        echo "⚠️ Found " . count($orphanedImpacts) . " impact activities with invalid event_id:\n";
        foreach ($orphanedImpacts as $record) {
            echo "  - ID: {$record['id']}, Title: {$record['title']}, Invalid Event ID: {$record['event_id']}\n";
        }
    }
    echo "\n";
    
    // Check for duplicate slugs
    echo "Checking for duplicate slugs in causes...\n";
    $qb = $db->createQueryBuilder();
    $duplicateSlugs = $qb->select('slug', 'COUNT(*) as count')
        ->from('causes')
        ->where('slug IS NOT NULL')
        ->groupBy('slug')
        ->having('COUNT(*) > 1')
        ->executeQuery()
        ->fetchAllAssociative();
    
    if (empty($duplicateSlugs)) {
        echo "✅ No duplicate slugs found\n";
    } else {
        echo "⚠️ Found duplicate slugs:\n";
        foreach ($duplicateSlugs as $record) {
            echo "  - Slug: {$record['slug']}, Count: {$record['count']}\n";
        }
    }
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Error during consistency tests: {$e->getMessage()}\n";
}

echo "=== Test Complete ===\n";

