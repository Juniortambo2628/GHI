<?php

namespace GHI\Services;

use GHI\Models\Initiative;

class InitiativesPageService
{
    private readonly Initiative $initiativeModel;

    private $db;

    private int $itemsPerPage = 12;

    public function __construct()
    {
        $this->initiativeModel = new Initiative();
        $this->db = \Database::getInstance();
    }

    /**
     * Get all data for initiatives page
     */
    public function getPageData(array $params = []): array
    {
        // Extract parameters
        $currentPage = max(1, $params['page'] ?? 1);
        $search = trim($params['search'] ?? '');
        $filterObjective = $params['filter']['objective'] ?? null;
        $filterStatus = $params['filter']['status'] ?? null;

        // Get all initiatives
        $allInitiatives = $this->getAllInitiatives();

        // Process initiatives (add event counts and map category to objective)
        $processedInitiatives = $this->processInitiatives($allInitiatives);

        // Apply filters
        $filteredInitiatives = $this->applyFilters($processedInitiatives, [
            'search' => $search,
            'objective' => $filterObjective,
            'status' => $filterStatus,
        ]);

        // Pagination
        $paginationData = $this->paginate($filteredInitiatives, $currentPage);

        // Build filter configuration
        $filters = $this->buildFilters();

        return [
            'initiatives' => $paginationData['items'],
            'totalItems' => $paginationData['total'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $currentPage,
            'itemsPerPage' => $this->itemsPerPage,
            'filters' => $filters,
            'search' => $search,
        ];
    }

    /**
     * Get all published initiatives from database (cached)
     */
    private function getAllInitiatives(): array
    {
        return cache_remember('initiatives_page.all_initiatives', fn(): array => $this->initiativeModel->all(['status' => 'published'], 'created_at DESC'), 3600); // 1 hour
    }

    /**
     * Process initiatives - add event counts and map category to objective
     */
    private function processInitiatives(array $initiatives): array
    {
        $categoryToObjective = [
            'livelihood' => 'poverty',
            'education' => 'education',
            'health' => 'health',
            'empowerment' => 'empowerment',
            'partnerships' => 'partnerships',
        ];

        foreach ($initiatives as &$initiative) {
            // Count events for this initiative
            $eventCount = $this->getEventCount($initiative['id']);
            $initiative['events_planned'] = $eventCount;
            $initiative['events_completed'] = $eventCount; // Simplified for now

            // Map category to objective
            $initiative['objective'] = $categoryToObjective[$initiative['category']] ?? 'education';
        }

        unset($initiative);

        return $initiatives;
    }

    /**
     * Get event count for an initiative (cached)
     */
    private function getEventCount(int $initiativeId): int
    {
        $cacheKey = sprintf('initiative.%d.event_count', $initiativeId);

        return cache_remember($cacheKey, function () use ($initiativeId): int {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM events WHERE initiative_id = ? AND status = 'published'");
            $stmt->execute([$initiativeId]);

            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        }, 3600); // 1 hour
    }

    /**
     * Apply search and filters to initiatives
     */
    private function applyFilters(array $initiatives, array $filters): array
    {
        $filtered = $initiatives;

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $filtered = array_filter($filtered, fn($initiative): bool => stripos((string) $initiative['title'], (string) $search) !== false
                || stripos((string) $initiative['description'], (string) $search) !== false);
        }

        // Objective filter
        if (!empty($filters['objective'])) {
            $filtered = array_filter($filtered, fn($initiative): bool => $initiative['objective'] === $filters['objective']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $filtered = array_filter($filtered, fn($initiative): bool => $initiative['status'] === $filters['status']);
        }

        return array_values($filtered);
    }

    /**
     * Build filter configuration
     */
    private function buildFilters(): array
    {
        return [
            'objective' => [
                'label' => 'Core Objective',
                'type' => 'select',
                'options' => [
                    'poverty' => 'Poverty Alleviation & Livelihoods',
                    'education' => 'Education Access & Youth Development',
                    'health' => 'Health & Well-being',
                    'empowerment' => 'Community Empowerment',
                    'partnerships' => 'Global Partnerships & Awareness',
                ],
            ],
            'status' => [
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    'active' => 'Active',
                    'completed' => 'Completed',
                    'planned' => 'Planned',
                ],
            ],
        ];
    }

    /**
     * Paginate results
     */
    private function paginate(array $items, int $currentPage): array
    {
        $total = count($items);
        $totalPages = ceil($total / $this->itemsPerPage);
        $offset = ($currentPage - 1) * $this->itemsPerPage;
        $paginatedItems = array_slice($items, $offset, $this->itemsPerPage);

        return [
            'items' => $paginatedItems,
            'total' => $total,
            'totalPages' => $totalPages,
        ];
    }
}
