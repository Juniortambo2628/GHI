<?php

namespace GHI\Services;

use GHI\Models\ImpactActivity;

class ImpactPageService
{
    private readonly ImpactActivity $impactModel;

    private int $itemsPerPage = 12;

    public function __construct()
    {
        $this->impactModel = new ImpactActivity();
    }

    /**
     * Get all data for impact page
     */
    public function getPageData(array $params = []): array
    {
        // Extract parameters
        $currentPage = max(1, $params['page'] ?? 1);
        $search = trim($params['search'] ?? '');
        $filterRegion = $params['filter']['region'] ?? null;
        $filterObjective = $params['filter']['objective'] ?? null;

        $filters = [
            'search' => $search,
            'region' => $filterRegion,
            'objective' => $filterObjective,
        ];

        // Get filtered and paginated data directly from database
        $paginationData = $this->impactModel->getFilteredImpacts($filters, $currentPage, $this->itemsPerPage);

        // Build filter configuration
        $filtersConfig = $this->buildFilters();

        return [
            'impacts' => $paginationData['items'],
            'totalItems' => $paginationData['total'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $currentPage,
            'itemsPerPage' => $this->itemsPerPage,
            'filters' => $filtersConfig,
            'search' => $search,
        ];
    }

    /**
     * Build filter configuration
     */
    private function buildFilters(): array
    {
        return [
            'region' => [
                'label' => 'Region',
                'type' => 'select',
                'options' => [
                    'kenya' => 'Kenya',
                    'tanzania' => 'Tanzania',
                    'uganda' => 'Uganda',
                    'rwanda' => 'Rwanda',
                ],
            ],
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
        ];
    }
}
