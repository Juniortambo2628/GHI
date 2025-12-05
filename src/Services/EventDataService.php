<?php
/**
 * Event Data Service
 * Global Harmony Initiative
 * 
 * Business logic layer for event data operations
 */

namespace GHI\Services;

use GHI\Repositories\EventRepository;

class EventDataService
{
    private EventRepository $repository;
    
    public function __construct()
    {
        $this->repository = new EventRepository();
    }
    
    /**
     * Get upcoming events for display
     */
    public function getUpcomingEvents(int $limit = 3): array
    {
        return $this->repository->findUpcoming($limit);
    }
    
    /**
     * Get event by ID with validation
     */
    public function getEventById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        
        return $this->repository->findById($id);
    }
    
    /**
     * Get all events with pagination
     */
    public function getAllEvents(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $events = $this->repository->findAll($filters, $perPage, $offset);
        $total = $this->repository->count($filters);
        
        return [
            'events' => $events,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }
    
    /**
     * Get recent events
     */
    public function getRecentEvents(int $limit = 5): array
    {
        return $this->repository->findRecent($limit);
    }
    
    /**
     * Create a new event
     */
    public function createEvent(array $data): int
    {
        // Add created_at timestamp
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'draft';
        
        return $this->repository->create($data);
    }
    
    /**
     * Update an event
     */
    public function updateEvent(int $id, array $data): bool
    {
        // Add updated_at timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->repository->updateEvent($id, $data);
    }
    
    /**
     * Delete an event
     */
    public function deleteEvent(int $id): bool
    {
        return $this->repository->deleteEvent($id);
    }
}
