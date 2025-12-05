<?php

/**
 * Base Model Class
 * Global Harmony Initiative Website
 */

namespace GHI\Models;

use Doctrine\DBAL\Connection;
use GHI\Services\DatabaseService;
use PDO;

abstract class BaseModel
{
    protected Connection $db;

    protected PDO $pdo;

     // For backward compatibility with direct PDO usage
    protected string $table;

    protected string $primaryKey = 'id';

    public function __construct()
    {
        require_once __DIR__ . '/../../config/config.php';
        require_once __DIR__ . '/../../config/database.php';
        $this->db = DatabaseService::getConnection();
        $this->pdo = DatabaseService::getPdo(); // For backward compatibility
    }

    /**
     * Get all records
     */
    public function all(array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from($this->table);

        // Add conditions
        if ($conditions !== []) {
            $first = true;
            foreach ($conditions as $field => $value) {
                $expr = $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($value));
                if ($first) {
                    $queryBuilder->where($expr);
                    $first = false;
                } else {
                    $queryBuilder->andWhere($expr);
                }
            }
        }

        // Add ordering
        if ($orderBy !== '' && $orderBy !== '0') {
            // Handle multiple order by clauses: "field1 ASC, field2 DESC" or single "field ASC"
            $orderClauses = array_map('trim', explode(',', $orderBy));
            $firstOrder = true;

            foreach ($orderClauses as $clause) {
                // Split each clause by space to get field and direction
                $parts = preg_split('/\s+/', trim($clause), 2);
                $field = $parts[0];
                $direction = isset($parts[1]) ? strtoupper($parts[1]) : 'ASC';

                // Validate direction
                if (!in_array($direction, ['ASC', 'DESC'])) {
                    $direction = 'ASC';
                }

                if ($firstOrder) {
                    $queryBuilder->orderBy($field, $direction);
                    $firstOrder = false;
                } else {
                    $queryBuilder->addOrderBy($field, $direction);
                }
            }
        }

        // Add limit and offset
        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
            if ($offset > 0) {
                $queryBuilder->setFirstResult($offset);
            }
        }

        return $queryBuilder->fetchAllAssociative();
    }

    /**
     * Find record by ID
     */
    public function find(int $id): ?array
    {
        $result = $this->db->fetchAssociative(
            sprintf('SELECT * FROM %s WHERE %s = ? LIMIT 1', $this->table, $this->primaryKey),
            [$id],
            [PDO::PARAM_INT]
        );

        return $result ?: null;
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $affectedRows = $this->db->update(
            $this->table,
            $data,
            [$this->primaryKey => $id],
            [$this->primaryKey => PDO::PARAM_INT]
        );

        return $affectedRows > 0;
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $affectedRows = $this->db->delete(
            $this->table,
            [$this->primaryKey => $id],
            [$this->primaryKey => PDO::PARAM_INT]
        );

        return $affectedRows > 0;
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('COUNT(*) as total')->from($this->table);

        // Add conditions
        if ($conditions !== []) {
            $first = true;
            foreach ($conditions as $field => $value) {
                $expr = $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($value));
                if ($first) {
                    $queryBuilder->where($expr);
                    $first = false;
                } else {
                    $queryBuilder->andWhere($expr);
                }
            }
        }

        $result = $queryBuilder->fetchAssociative();

        return (int)($result['total'] ?? 0);
    }
}
