<?php

namespace GHI\Services;

use GHI\Models\Initiative;
use GHI\Models\Event;
use GHI\Models\Story;
use GHI\Models\ImpactActivity;

/**
 * Home Page Service
 * Centralizes all data fetching for the homepage
 */
class HomePageService
{
    private readonly Initiative $initiativeModel;

    private readonly Event $eventModel;

    private readonly Story $storyModel;

    private readonly ImpactActivity $impactModel;

    public function __construct()
    {
        $this->initiativeModel = new Initiative();
        $this->eventModel = new Event();
        $this->storyModel = new Story();
        $this->impactModel = new ImpactActivity();
    }

    /**
     * Get all data needed for the homepage
     *
     * @return array Associative array with all homepage data
     */
    public function getPageData(): array
    {
        return [
            // Static content from constants
            'objectives' => OBJECTIVES,
            'coreValues' => CORE_VALUES,
            'quotes' => INSPIRATIONAL_QUOTES,
            'randomQuote' => $this->getRandomQuote(),

            // Dynamic content from database (with caching)
            'initiatives' => $this->getInitiatives(),
            'impactStories' => $this->getImpactStories(),
            'stories' => $this->getStories(),
            'upcomingEvents' => $this->getUpcomingEvents(),
            'recentActivities' => $this->getRecentActivities(),
            'allInitiatives' => $this->getAllInitiatives(),
            'initiativesById' => $this->getInitiativesByIdMap(),

            // Category mapping
            'categoryToObjective' => $this->getCategoryToObjectiveMap(),

            // Counters
            'counters' => $this->getCounters(),
        ];
    }

    /**
     * Get a random inspirational quote
     *
     * @return array Quote array with 'text' and 'author'
     */
    private function getRandomQuote(): array
    {
        $quotes = INSPIRATIONAL_QUOTES;
        return $quotes[array_rand($quotes)];
    }

    /**
     * Get published initiatives for homepage (limit 3)
     * Cached for 1 hour
     * Includes event counts for each initiative
     */
    private function getInitiatives(): array
    {
        $initiatives = cache_remember('homepage.initiatives', fn(): array => $this->initiativeModel->all(['status' => 'published'], 'created_at DESC', 3), 3600);

        // Pre-fetch all event counts in a single query to avoid N+1 problem
        $eventCounts = $this->getEventCountsByInitiative(array_column($initiatives, 'id'));

        // Add event counts to each initiative
        foreach ($initiatives as &$initiative) {
            $initiative['event_count'] = $eventCounts[$initiative['id']] ?? 0;
        }

        unset($initiative);

        return $initiatives;
    }

    /**
     * Get event counts for multiple initiatives in a single query
     * Prevents N+1 query problem
     *
     * @param array $initiativeIds Array of initiative IDs
     * @return array Associative array [initiative_id => count]
     */
    private function getEventCountsByInitiative(array $initiativeIds): array
    {
        if ($initiativeIds === []) {
            return [];
        }

        $db = \GHI\Services\DatabaseService::getPdo();
        $placeholders = implode(',', array_fill(0, count($initiativeIds), '?'));

        $stmt = $db->prepare("
            SELECT initiative_id, COUNT(*) as total 
            FROM events 
            WHERE initiative_id IN ({$placeholders}) 
            AND status = 'published'
            GROUP BY initiative_id
        ");
        $stmt->execute($initiativeIds);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $counts = [];
        foreach ($results as $row) {
            $counts[$row['initiative_id']] = (int)$row['total'];
        }

        // Ensure all initiative IDs have a count (even if 0)
        foreach ($initiativeIds as $id) {
            if (!isset($counts[$id])) {
                $counts[$id] = 0;
            }
        }

        return $counts;
    }

    /**
     * Get impact stories for "Planting Seeds of Hope" section (limit 3)
     * Cached for 1 hour
     */
    private function getImpactStories(): array
    {
        return cache_remember('homepage.impact_stories', fn(): array => $this->impactModel->all(['status' => 'published'], 'display_order ASC, created_at DESC', 3), 3600);
    }

    /**
     * Get stories for "Our Impact" section (limit 3)
     * Cached for 1 hour
     */
    private function getStories(): array
    {
        return cache_remember('homepage.stories', fn(): array => $this->storyModel->all(['status' => 'published'], 'created_at DESC', 3), 3600);
    }

    /**
     * Get upcoming events (limit 3)
     * Cached for 30 minutes
     * Includes initiative name and status
     */
    private function getUpcomingEvents(): array
    {
        $events = cache_remember('homepage.upcoming_events', fn(): array => $this->eventModel->getUpcoming(3), 1800);

        // Get initiatives map for adding initiative names
        $initiativesById = $this->getInitiativesByIdMap();

        // Process events to add initiative name and status
        foreach ($events as &$event) {
            // Map event_date to date for view compatibility
            $event['date'] = $event['event_date'] ?? '';
            
            // Add initiative name
            $event['initiative'] = isset($event['initiative_id']) && isset($initiativesById[$event['initiative_id']]) 
                ? $initiativesById[$event['initiative_id']]['title'] 
                : 'General';

            // Determine status based on date
            $today = date('Y-m-d');
            $event['status'] = ($event['event_date'] ?? '') >= $today ? 'upcoming' : 'completed';
        }

        unset($event);

        return $events;
    }

    /**
     * Get recent activities for gallery (limit 5)
     * Cached for 1 hour
     * Enriched with initiative names and objectives
     */
    private function getRecentActivities(): array
    {
        $events = cache_remember('homepage.recent_events', fn(): array => $this->eventModel->all(['status' => 'published'], 'event_date DESC', 3), 3600);

        // Get initiatives for mapping
        $initiativesById = $this->getInitiativesByIdMap();
        $categoryToObjective = $this->getCategoryToObjectiveMap();
        $objectiveLabels = [
            'poverty' => 'Poverty Alleviation & Livelihoods',
            'education' => 'Education Access & Youth Development',
            'health' => 'Health & Well-being',
            'empowerment' => 'Community Empowerment',
            'partnerships' => 'Global Partnerships & Awareness',
        ];

        // Enrich events with initiative name and objective
        foreach ($events as &$event) {
            $initiative = $initiativesById[$event['initiative_id']] ?? null;
            if ($initiative) {
                $event['initiative'] = $initiative['title'];
                $objectiveKey = $categoryToObjective[$initiative['category']] ?? 'education';
                $event['objective'] = $objectiveLabels[$objectiveKey] ?? 'Community Development';
            } else {
                $event['initiative'] = 'N/A';
                $event['objective'] = 'Community Development';
            }
        }

        unset($event);

        return array_slice($events, 0, 5); // Limit to 5 for gallery
    }

    /**
     * Get all published initiatives
     * Cached for 1 hour
     */
    private function getAllInitiatives(): array
    {
        return cache_remember('homepage.all_initiatives', fn(): array => $this->initiativeModel->all(['status' => 'published'], 'title ASC'), 3600);
    }

    /**
     * Get initiatives indexed by ID for quick lookup
     */
    private function getInitiativesByIdMap(): array
    {
        $allInitiatives = $this->getAllInitiatives();
        $initiativesById = [];

        foreach ($allInitiatives as $init) {
            $initiativesById[$init['id']] = $init;
        }

        return $initiativesById;
    }

    /**
     * Get category to objective mapping
     */
    private function getCategoryToObjectiveMap(): array
    {
        return [
            'livelihood' => 'Poverty Alleviation & Livelihoods',
            'education' => 'Education Access & Youth Development',
            'health' => 'Health & Well-being',
            'empowerment' => 'Community Empowerment',
            'partnerships' => 'Global Partnerships & Awareness',
        ];
    }

    /**
     * Get counter statistics for homepage
     * Cached for 1 hour
     */
    private function getCounters(): array
    {
        return [
            'initiatives' => cache_remember('homepage.initiatives_count', fn(): int => $this->initiativeModel->count(['status' => 'published']), 3600),

            'events' => cache_remember('homepage.events_count', fn(): int => $this->eventModel->count(['status' => 'published']), 3600),

            'lives_impacted' => cache_remember('homepage.lives_impacted', fn(): int => $this->impactModel->getTotalLivesImpacted(), 3600),

            'communities' => cache_remember('homepage.communities_count', fn(): int => $this->impactModel->getCommunitiesCount(), 3600),
        ];
    }
}
