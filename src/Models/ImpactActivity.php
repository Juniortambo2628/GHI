<?php

/**
 * Impact Activity Model
 * Global Harmony Initiative Website
 * 
 * Now uses ImpactActivityRepository internally for all database operations
 */

namespace GHI\Models;

use GHI\Repositories\ImpactActivityRepository;

class ImpactActivity extends BaseModel
{
    protected string $table = 'impact_activities';
    private ImpactActivityRepository $repository;
    
    public function __construct()
    {
        parent::__construct();
        $this->repository = new ImpactActivityRepository();
    }

    /**
     * Get impact activities by event
     */
    public function getByEvent(int $eventId): array
    {
        return $this->repository->findByEvent($eventId);
    }

    /**
     * Get total people affected
     */
    public function getTotalPeopleAffected(): int
    {
        return $this->repository->getTotalPeopleAffected();
    }

    /**
     * Get total lives impacted (alias for getTotalPeopleAffected)
     */
    public function getTotalLivesImpacted(): int
    {
        return $this->getTotalPeopleAffected();
    }

    /**
     * Get count of unique communities reached
     */
    public function getCommunitiesCount(): int
    {
        return $this->repository->getCommunitiesCount();
    }

    /**
     * Get filtered impact activities with pagination
     */
    public function getFilteredImpacts(array $filters = [], int $page = 1, int $limit = 12): array
    {
        return $this->repository->findFilteredWithJoins($filters, $page, $limit);
    }
}
