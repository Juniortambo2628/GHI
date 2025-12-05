<?php

namespace GHI\Models;

use GHI\Models\BaseModel;

class Donation extends BaseModel
{
    public $conn;
    protected string $table = 'donations';

    /**
     * Create a new donation
     */
    public function create(array $data): int
    {
        $defaultData = [
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $data = array_merge($defaultData, $data);

        return parent::insert($data);
    }

    /**
     * Get all donations with pagination
     */
    public function getAllWithPagination(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = sprintf('SELECT * FROM %s ORDER BY created_at DESC LIMIT ? OFFSET ?', $this->table);
        $donations = $this->conn->fetchAllAssociative($sql, [$perPage, $offset]);

        $totalSql = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $total = $this->conn->fetchOne($totalSql);

        return [
            'items' => $donations,
            'total' => (int) $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get donation statistics
     */
    public function getStatistics(): array
    {
        $sql = 'SELECT 
            COUNT(*) as total_donations,
            SUM(CASE WHEN status = \'completed\' THEN amount ELSE 0 END) as total_amount,
            AVG(CASE WHEN status = \'completed\' THEN amount ELSE NULL END) as avg_amount,
            COUNT(CASE WHEN status = \'completed\' THEN 1 END) as completed_donations,
            COUNT(CASE WHEN status = \'pending\' THEN 1 END) as pending_donations,
            COUNT(CASE WHEN donation_type = \'monthly\' THEN 1 END) as recurring_donations
        FROM ' . $this->table;

        return $this->conn->fetchAssociative($sql) ?: [];
    }

    /**
     * Get recent donations
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = sprintf('SELECT * FROM %s ORDER BY created_at DESC LIMIT ?', $this->table);
        return $this->conn->fetchAllAssociative($sql, [$limit]);
    }

    /**
     * Update donation status
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
