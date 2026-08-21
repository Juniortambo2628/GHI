<?php

namespace App\Http\Controllers;

use App\Models\Initiative;
use App\Models\Event;
use App\Models\Story;
use App\Models\ImpactActivity;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $initiatives = Initiative::published()->latest()->limit(3)->get();
        $events = Event::upcoming()->limit(3)->get();
        $stories = Story::published()->latest()->limit(3)->get();
        $impactStories = ImpactActivity::published()->limit(3)->get();
        $recentEvents = Event::published()->latest()->limit(5)->get();

        // Enrich initiatives with event counts
        $initiativeIds = $initiatives->pluck('id')->toArray();
        $eventCounts = Event::whereIn('initiative_id', $initiativeIds)
            ->where('status', 'published')
            ->selectRaw('initiative_id, COUNT(*) as total')
            ->groupBy('initiative_id')
            ->pluck('total', 'initiative_id')
            ->toArray();

        $enrichedInitiatives = $initiatives->map(function ($initiative) use ($eventCounts) {
            $obj = $initiative->toArray();
            $obj['event_count'] = $eventCounts[$initiative->id] ?? 0;
            $obj['objective'] = config('site.category_to_objective.' . $initiative->category, 'Community Development');
            return $obj;
        });

        // Enrich events with initiative names
        $allInitiatives = Initiative::published()->pluck('title', 'id')->toArray();
        $enrichedEvents = $events->map(function ($event) use ($allInitiatives) {
            $obj = $event->toArray();
            $obj['initiative'] = $allInitiatives[$event->initiative_id] ?? 'General';
            $obj['date'] = $event->event_date;
            return $obj;
        });

        // Enrich recent events for gallery
        $enrichedActivities = $recentEvents->map(function ($event) use ($allInitiatives) {
            $obj = $event->toArray();
            $obj['initiative'] = $allInitiatives[$event->initiative_id] ?? 'N/A';
            $initiative = Initiative::find($event->initiative_id);
            $obj['objective'] = $initiative ? config('site.category_to_objective.' . $initiative->category, 'Community Development') : 'Community Development';
            $obj['location'] = $event->location;
            return $obj;
        });

        // Enrich stories
        $enrichedStories = $stories->map(function ($story) {
            $obj = $story->toArray();
            $obj['objective'] = config('site.category_to_objective.' . $story->category, 'Community Development');
            $obj['slug'] = $story->slug ?? 'story-' . $story->id;
            return $obj;
        });

        // Counters
        $stats = [
            'initiatives' => Initiative::published()->count(),
            'events' => Event::published()->count(),
            'communities' => ImpactActivity::published()->distinct('event_id')->count('event_id'),
            'lives_impacted' => ImpactActivity::published()->sum('people_affected'),
        ];

        // Random quote
        $quotes = config('site.quotes', []);
        $randomQuote = $quotes[array_rand($quotes)] ?? ['quote' => '', 'author' => ''];

        return inertia('Home', [
            'initiatives' => $enrichedInitiatives,
            'events' => $enrichedEvents,
            'stories' => $enrichedStories,
            'impactStories' => $impactStories,
            'recentActivities' => $enrichedActivities,
            'stats' => $stats,
            'counters' => $stats,
            'randomQuote' => $randomQuote,
            'objectives' => config('site.objectives', []),
            'coreValues' => config('site.core_values', []),
            'categoryToObjective' => config('site.category_to_objective', []),
        ]);
    }
}
