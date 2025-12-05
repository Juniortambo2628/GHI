<?php
/**
 * Impact Activity Repository
 * Global Harmony Initiative
 * 
 * Centralized data access for impact activities
 * Handles complex queries with JOINs and aggregations
 */

namespace GHI\Repositories;

class ImpactActivityRepository extends BaseRepository
{
    /**
     * Find impact activities by event
     */
    public function findByEvent(int $eventId): array
    {
        $sql = "SELECT * FROM impact_activities 
                WHERE event_id = :event_id 
                AND status = 'published'
                ORDER BY display_order ASC, created_at DESC";
        
        return $this->fetchAll($sql, ['event_id' => $eventId]);
    }
    
    /**
     * Get total people affected (aggregate)
     */
    public function getTotalPeopleAffected(): int
    {
        $sql = "SELECT SUM(people_affected) as total 
                FROM impact_activities 
                WHERE status = :status";
        
        $result = (int) $this->fetchColumn($sql, ['status' => 'published']);
        return $result ?: 0;
    }
    
    /**
     * Get count of unique communities reached
     */
    public function getCommunitiesCount(): int
    {
        $sql = "SELECT COUNT(DISTINCT event_id) as total 
                FROM impact_activities 
                WHERE status = :status 
                AND event_id IS NOT NULL";
        
        $result = (int) $this->fetchColumn($sql, ['status' => 'published']);
        return $result ?: 0;
    }
    
    /**
     * Get filtered impact activities with JOINs
     * Complex query joining impact_activities, events, and initiatives tables
     */
    public function findFilteredWithJoins(array $filters = [], int $page = 1, int $limit = 12): array
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder
            ->select(
                'i.*',
                'e.title as event_title',
                'e.event_date',
                'e.location',
                'init.title as initiative_title',
                'init.category as initiative_category'
            )
            ->from('impact_activities', 'i')
            ->leftJoin('i', 'events', 'e', 'i.event_id = e.id')
            ->leftJoin('e', 'initiatives', 'init', 'e.initiative_id = init.id')
            ->where('i.status = :status')
            ->setParameter('status', 'published');
        
        // Search filter
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $queryBuilder->andWhere(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->like('i.title', ':search'),
                    $queryBuilder->expr()->like('i.description', ':search'),
                    $queryBuilder->expr()->like('e.location', ':search')
                )
            )->setParameter('search', $search);
        }
        
        // Region filter (based on event location)
        if (!empty($filters['region'])) {
            $queryBuilder->andWhere('e.location LIKE :region')
                ->setParameter('region', '%' . $filters['region'] . '%');
        }
        
        // Objective filter (based on initiative category)
        if (!empty($filters['objective'])) {
            $objectiveToCategory = [
                'poverty' => 'livelihood',
                'education' => 'education',
                'health' => 'health',
                'empowerment' => 'empowerment',
                'partnerships' => 'partnerships',
            ];
            
            $category = $objectiveToCategory[$filters['objective']] ?? $filters['objective'];
            $queryBuilder->andWhere('init.category = :category')
                ->setParameter('category', $category);
        }
        
        // Count total results
        $countQuery = clone $queryBuilder;
        $countQuery->select('COUNT(DISTINCT i.id)');
        $total = (int) $countQuery->fetchOne();
        
        // Add ordering
        $queryBuilder->orderBy('i.display_order', 'ASC')
            ->addOrderBy('i.created_at', 'DESC');
        
        // Pagination
        $offset = ($page - 1) * $limit;
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit);
        
        $items = $queryBuilder->fetchAllAssociative();
        
        // Post-process items
        $items = $this->enrichImpactActivities($items);
        
        return [
            'items' => $items,
            'total' => $total,
            'totalPages' => (int) ceil($total / $limit),
        ];
    }
    
    /**
     * Enrich impact activities with computed fields
     * 
     * @param array $items Raw impact activity data
     * @return array Enriched impact activities
     */
    private function enrichImpactActivities(array $items): array
    {
        $categoryToObjective = [
            'livelihood' => 'poverty',
            'education' => 'education',
            'health' => 'health',
            'empowerment' => 'empowerment',
            'partnerships' => 'partnerships',
        ];
        
        foreach ($items as &$item) {
            // Region (extract from location)
            $location = strtolower($item['location'] ?? '');
            if (str_contains($location, 'kenya')) {
                $item['region'] = 'kenya';
            } elseif (str_contains($location, 'tanzania')) {
                $item['region'] = 'tanzania';
            } elseif (str_contains($location, 'uganda')) {
                $item['region'] = 'uganda';
            } elseif (str_contains($location, 'rwanda')) {
                $item['region'] = 'rwanda';
            } else {
                $item['region'] = 'kenya';
            }
            
            // Objective (map from category)
            $category = $item['initiative_category'] ?? '';
            $item['objective'] = $categoryToObjective[$category] ?? 'education';
            
            // Lives impacted (alias)
            $item['lives_impacted'] = $item['people_affected'] ?? 0;
            
            // Date (prefer event date)
            $item['date'] = $item['event_date'] ?? $item['created_at'] ?? '';
        }
        
        unset($item);
        return $items;
    }
}
