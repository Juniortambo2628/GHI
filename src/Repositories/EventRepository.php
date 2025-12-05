<?php
/**
 * Event Repository
 * Global Harmony Initiative
 * 
 * Centralized data access for events
 */

namespace GHI\Repositories;

class EventRepository extends BaseRepository
{
    /**
     * Find upcoming events
     */
    public function findUpcoming(int $limit = 10): array
    {
        $sql = "SELECT * FROM events 
                WHERE event_date >= CURDATE() 
                AND status = 'published'
                ORDER BY event_date ASC 
                LIMIT :limit";
        
        return $this->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Find event by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM events WHERE id = :id";
        return $this->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Find all events with optional filters
     */
    public function findAll(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM events WHERE 1=1";
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (isset($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (isset($filters['initiative'])) {
            $sql .= " AND initiative = :initiative";
            $params['initiative'] = $filters['initiative'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY event_date DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        
        return $this->fetchAll($sql, $params);
    }
    
    /**
     * Get total count with filters
     */
    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM events WHERE 1=1";
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (isset($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        return (int) $this->fetchColumn($sql, $params);
    }
    
    /**
     * Create a new event
     */
    public function create(array $data): int
    {
        return $this->insert('events', $data);
    }
    
    /**
     * Update an event
     */
    public function updateEvent(int $id, array $data): bool
    {
        return $this->update('events', $data, ['id' => $id]) > 0;
    }
    
    /**
     * Delete an event
     */
    public function deleteEvent(int $id): bool
    {
        return $this->delete('events', ['id' => $id]) > 0;
    }
    
    /**
     * Find recent events
     */
    public function findRecent(int $limit = 5): array
    {
        $sql = "SELECT * FROM events 
                WHERE status = 'published'
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->fetchAll($sql, ['limit' => $limit]);
    }
}
