<?php

namespace GHI\Services;

use GHI\Models\Event;
use GHI\Models\Initiative;

class EventsPageService
{
    private readonly Event $eventModel;

    private readonly Initiative $initiativeModel;

    private int $itemsPerPage = 12;

    public function __construct()
    {
        $this->eventModel = new Event();
        $this->initiativeModel = new Initiative();
    }

    /**
     * Get all data for events page
     */
    public function getPageData(array $params = []): array
    {
        // Extract parameters
        $currentPage = max(1, $params['page'] ?? 1);
        $search = trim($params['search'] ?? '');
        $filterInitiative = $params['filter']['initiative'] ?? null;
        $filterStatus = $params['filter']['status'] ?? null;

        // Get all events
        $allEvents = $this->getAllEvents();

        // Get initiatives for filter options
        $initiatives = $this->getInitiatives();
        $initiativesById = $this->buildInitiativesMap($initiatives);

        // Process events (add initiative name and status)
        $processedEvents = $this->processEvents($allEvents, $initiativesById);

        // Apply filters
        $filteredEvents = $this->applyFilters($processedEvents, [
            'search' => $search,
            'initiative' => $filterInitiative,
            'status' => $filterStatus,
        ]);

        // Pagination
        $paginationData = $this->paginate($filteredEvents, $currentPage);

        // Build filter configuration
        $filters = $this->buildFilters($initiatives);

        return [
            'events' => $paginationData['items'],
            'totalItems' => $paginationData['total'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $currentPage,
            'itemsPerPage' => $this->itemsPerPage,
            'filters' => $filters,
            'search' => $search,
            'initiatives' => $initiatives,
        ];
    }

    /**
     * Get all published events from database (cached)
     */
    private function getAllEvents(): array
    {
        return cache_remember('events_page.all_events', fn(): array => $this->eventModel->all(['status' => 'published'], 'event_date DESC'), 1800); // 30 minutes
    }

    /**
     * Get all published initiatives (cached)
     */
    private function getInitiatives(): array
    {
        return cache_remember('events_page.initiatives', fn(): array => $this->initiativeModel->all(['status' => 'published'], 'title ASC'), 3600); // 1 hour
    }

    /**
     * Build initiatives lookup map
     */
    private function buildInitiativesMap(array $initiatives): array
    {
        $map = [];
        foreach ($initiatives as $init) {
            $map[$init['id']] = $init;
        }

        return $map;
    }

    /**
     * Process events - add initiative name and determine status
     */
    private function processEvents(array $events, array $initiativesById): array
    {
        $today = date('Y-m-d');

        foreach ($events as &$event) {
            $event['initiative'] = $initiativesById[$event['initiative_id']]['title'] ?? 'N/A';
            $event['date'] = $event['event_date'];

            // Determine status based on date
            $event['status'] = $event['event_date'] >= $today ? 'upcoming' : 'completed';
        }

        unset($event);

        return $events;
    }

    /**
     * Apply search and filters to events
     */
    private function applyFilters(array $events, array $filters): array
    {
        $filtered = $events;

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $filtered = array_filter($filtered, fn($event): bool => stripos((string) $event['title'], (string) $search) !== false
                || stripos((string) $event['description'], (string) $search) !== false);
        }

        // Initiative filter
        if (!empty($filters['initiative'])) {
            $filtered = array_filter($filtered, fn($event): bool => $event['initiative_id'] == $filters['initiative']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $filtered = array_filter($filtered, fn($event): bool => $event['status'] === $filters['status']);
        }

        return array_values($filtered);
    }

    /**
     * Build filter configuration
     */
    private function buildFilters(array $initiatives): array
    {
        $initiativeOptions = [];
        foreach ($initiatives as $init) {
            $initiativeOptions[$init['id']] = $init['title'];
        }

        return [
            'initiative' => [
                'label' => 'Initiative',
                'type' => 'select',
                'options' => $initiativeOptions,
            ],
            'status' => [
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    'upcoming' => 'Upcoming',
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
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
