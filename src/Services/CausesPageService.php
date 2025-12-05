<?php

namespace GHI\Services;

use GHI\Models\Cause;

class CausesPageService
{
    private readonly Cause $causeModel;

    private int $itemsPerPage = 12;

    public function __construct()
    {
        $this->causeModel = new Cause();
    }

    /**
     * Get all data for causes page
     */
    public function getPageData(array $params = []): array
    {
        // Extract parameters
        $currentPage = max(1, $params['page'] ?? 1);
        $search = trim($params['search'] ?? '');
        $filterObjective = $params['filter']['objective'] ?? null;
        $filterStatus = $params['filter']['status'] ?? null;

        // Get all causes
        $allCauses = $this->getAllCauses();

        // Apply filters
        $filteredCauses = $this->applyFilters($allCauses, [
            'search' => $search,
            'objective' => $filterObjective,
            'status' => $filterStatus,
        ]);

        // Pagination
        $paginationData = $this->paginate($filteredCauses, $currentPage);

        // Build filter configuration
        $filters = $this->buildFilters();

        return [
            'causes' => $paginationData['items'],
            'totalItems' => $paginationData['total'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $currentPage,
            'itemsPerPage' => $this->itemsPerPage,
            'filters' => $filters,
            'search' => $search,
        ];
    }

    /**
     * Get all active causes from database (cached)
     */
    private function getAllCauses(): array
    {
        return cache_remember('causes_page.all_causes', fn(): array => $this->causeModel->all(['status' => 'active'], 'display_order ASC, created_at DESC'), 3600); // 1 hour
    }

    /**
     * Apply search and filters to causes
     */
    private function applyFilters(array $causes, array $filters): array
    {
        $filtered = $causes;

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $filtered = array_filter($filtered, fn($cause): bool => stripos((string) $cause['title'], (string) $search) !== false
                || stripos((string) $cause['description'], (string) $search) !== false);
        }

        // Objective filter
        if (!empty($filters['objective'])) {
            $filtered = array_filter($filtered, fn($cause): bool => $cause['objective'] === $filters['objective']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $filtered = array_filter($filtered, fn($cause): bool => $cause['status'] === $filters['status']);
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
                    'upcoming' => 'Upcoming',
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
