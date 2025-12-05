<?php
/**
 * Base Repository
 * Global Harmony Initiative
 * 
 * Abstract base class for all repositories
 * Provides common database operations
 */

namespace GHI\Repositories;

use Doctrine\DBAL\Connection;
use GHI\Services\DatabaseService;

abstract class BaseRepository
{
    protected Connection $db;
    
    public function __construct()
    {
        $this->db = DatabaseService::getConnection();
    }
    
    /**
     * Execute a query and return the result
     */
    protected function query(string $sql, array $params = [])
    {
        return $this->db->executeQuery($sql, $params);
    }
    
    /**
     * Fetch all rows from a query
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAllAssociative();
    }
    
    /**
     * Fetch a single row from a query
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetchAssociative();
        return $result === false ? null : $result;
    }
    
    /**
     * Fetch a single column value
     */
    protected function fetchColumn(string $sql, array $params = [])
    {
        $result = $this->query($sql, $params)->fetchOne();
        return $result === false ? null : $result;
    }
    
    /**
     * Insert a record
     */
    protected function insert(string $table, array $data): int
    {
        $this->db->insert($table, $data);
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * Update records
     */
    protected function update(string $table, array $data, array $criteria): int
    {
        return $this->db->update($table, $data, $criteria);
    }
    
    /**
     * Delete records
     */
    protected function delete(string $table, array $criteria): int
    {
        return $this->db->delete($table, $criteria);
    }
}
