<?php

/**
 * Event Model
 * Global Harmony Initiative Website
 * 
 * Now uses EventRepository internally for all database operations
 */

namespace GHI\Models;

use GHI\Repositories\EventRepository;

class Event extends BaseModel
{
    protected string $table = 'events';
    private EventRepository $repository;
    
    public function __construct()
    {
        parent::__construct();
        $this->repository = new EventRepository();
    }

    /**
     * Get events by initiative
     */
    public function getByInitiative(int $initiativeId): array
    {
        return $this->repository->findAll(['initiative_id' => $initiativeId, 'status' => 'published']);
    }

    /**
     * Get upcoming events
     */
    public function getUpcoming(int $limit = 0): array
    {
        return $this->repository->findUpcoming($limit > 0 ? $limit : 1000);
    }

    /**
     * Get past events
     */
    public function getPast(int $limit = 0): array
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from($this->table)
            ->where('status = :status')
            ->andWhere('event_date < CURDATE()')
            ->orderBy('event_date', 'DESC')
            ->setParameter('status', 'published');

        if ($limit > 0) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->fetchAllAssociative();
    }
    
    /**
     * Find by ID (override to use repository)
     */
    public function find(int $id): ?array
    {
        return $this->repository->findById($id);
    }
    
    /**
     * Get count (override to use repository)
     */
    public function count(array $conditions = []): int
    {
        return $this->repository->count($conditions);
    }
}
