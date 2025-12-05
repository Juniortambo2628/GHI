<?php

namespace GHI\Services;

use GHI\Models\Story;

class StoriesPageService
{
    private readonly Story $storyModel;

    private int $itemsPerPage = 12;

    public function __construct()
    {
        $this->storyModel = new Story();
    }

    /**
     * Get all data for stories page
     */
    public function getPageData(array $params = []): array
    {
        // Extract parameters
        $currentPage = max(1, $params['page'] ?? 1);
        $search = trim($params['search'] ?? '');
        $filterRegion = $params['filter']['region'] ?? null;
        $filterCategory = $params['filter']['category'] ?? null;

        // Get all stories
        $allStories = $this->getAllStories();

        // Process stories (add additional fields)
        $processedStories = $this->processStories($allStories);

        // Apply filters
        $filteredStories = $this->applyFilters($processedStories, [
            'search' => $search,
            'region' => $filterRegion,
            'category' => $filterCategory,
        ]);

        // Pagination
        $paginationData = $this->paginate($filteredStories, $currentPage);

        // Build filter configuration
        $filters = $this->buildFilters();

        return [
            'stories' => $paginationData['items'],
            'totalItems' => $paginationData['total'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $currentPage,
            'itemsPerPage' => $this->itemsPerPage,
            'filters' => $filters,
            'search' => $search,
        ];
    }

    /**
     * Get all published stories from database (cached)
     */
    private function getAllStories(): array
    {
        return cache_remember('stories_page.all_stories', fn(): array => $this->storyModel->all(['status' => 'published'], 'created_at DESC'), 3600); // 1 hour
    }

    /**
     * Process stories - add additional fields for compatibility
     */
    private function processStories(array $stories): array
    {
        foreach ($stories as &$story) {
            $story['date'] = $story['created_at'];
            $story['author'] = 'GHI Team';
            $story['shares'] = 0; // Not in database yet

            // Map category - stories use same categories as initiatives
            if (!in_array($story['category'], ['education', 'health', 'empowerment'])) {
                $story['category'] = 'community'; // Default for other categories
            }

            // Default region (can be enhanced with actual data)
            $story['region'] ??= 'kenya';
        }

        unset($story);

        return $stories;
    }

    /**
     * Apply search and filters to stories
     */
    private function applyFilters(array $stories, array $filters): array
    {
        $filtered = $stories;

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $filtered = array_filter($filtered, fn($story): bool => stripos((string) $story['title'], (string) $search) !== false
                || stripos((string) $story['description'], (string) $search) !== false);
        }

        // Region filter
        if (!empty($filters['region'])) {
            $filtered = array_filter($filtered, fn($story): bool => $story['region'] === $filters['region']);
        }

        // Category filter
        if (!empty($filters['category'])) {
            $filtered = array_filter($filtered, fn($story): bool => $story['category'] === $filters['category']);
        }

        return array_values($filtered);
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
            'category' => [
                'label' => 'Category',
                'type' => 'select',
                'options' => [
                    'education' => 'Education',
                    'health' => 'Health',
                    'community' => 'Community',
                    'empowerment' => 'Empowerment',
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
