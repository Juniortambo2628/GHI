<?php

namespace App\Http\Controllers;

use App\Models\Cause;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\SiteSetting;
use App\Models\Story;

class HomeController extends Controller
{
    public function index()
    {
        $initiatives = Initiative::published()->latest()->limit(3)->get();
        $events = Event::published()->orderBy('event_date', 'desc')->limit(3)->get();
        $stories = Story::published()->latest()->limit(3)->get();
        $impactStories = ImpactActivity::published()->limit(3)->get();
        $recentEvents = Event::published()->orderBy('event_date', 'desc')->limit(5)->get();
        $causes = Cause::published()->orderBy('display_order')->get();

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
            $obj['objective'] = config('site.category_to_objective.'.$initiative->category, 'Community Development');

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

        // Build gallery: collect all images from recent published events
        $galleryImages = collect();
        $enrichedActivities = $recentEvents->map(function ($event) use ($allInitiatives) {
            $obj = $event->toArray();
            $obj['initiative'] = $allInitiatives[$event->initiative_id] ?? 'N/A';
            $initiative = Initiative::find($event->initiative_id);
            $obj['objective'] = $initiative ? config('site.category_to_objective.'.$initiative->category, 'Community Development') : 'Community Development';
            $obj['location'] = $event->location;
            $obj['gallery_images'] = $event->images()->orderBy('sort_order')->get()->map(fn ($img) => ['id' => $img->id, 'path' => $img->path]);

            return $obj;
        });

        // Build gallery: group images by event
        $allGalleryImages = $recentEvents->map(function ($event) use ($allInitiatives) {
            $images = $event->images()->orderBy('sort_order')->get()->map(fn ($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'type' => $img->type ?? 'image',
            ]);

            return [
                'event_id' => $event->id,
                'event_title' => $event->title,
                'initiative' => Initiative::find($event->initiative_id)?->title ?? 'N/A',
                'location' => $event->location,
                'event_date' => $event->event_date,
                'images' => $images->values(),
                'cover' => $images->first()['path'] ?? null,
            ];
        })->filter(fn ($item) => $item['images']->count() > 0)->values()->take(6);

        // Enrich stories
        $enrichedStories = $stories->map(function ($story) {
            $obj = $story->toArray();
            $obj['objective'] = config('site.category_to_objective.'.$story->category, 'Community Development');
            $obj['slug'] = $story->slug ?? 'story-'.$story->id;

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
        $cmsSettings = SiteSetting::grouped(config('site_settings'));

        // Videos from published events
        $videos = EventImage::where('type', 'video')
            ->whereHas('event', fn ($q) => $q->where('status', 'published'))
            ->with('event')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'title' => $img->event?->title ?? 'Event Video',
                'event_title' => $img->event?->title,
                'event_id' => $img->event_id,
                'event_date' => $img->event?->event_date,
                'image' => $img->event?->image,
            ]);

        return inertia('Home', [
            'initiatives' => $enrichedInitiatives,
            'events' => $enrichedEvents,
            'stories' => $enrichedStories,
            'impactStories' => $impactStories,
            'recentActivities' => $enrichedActivities,
            'galleryImages' => $allGalleryImages->values(),
            'videos' => $videos,
            'causes' => $causes,
            'stats' => $stats,
            'counters' => $stats,
            'randomQuote' => $randomQuote,
            'objectives' => config('site.objectives', []),
            'coreValues' => config('site.core_values', []),
            'categoryToObjective' => config('site.category_to_objective', []),
            'heroSlides' => json_decode($cmsSettings['homepage_hero'], true) ?: [],
            'sectionVisibility' => json_decode($cmsSettings['homepage_sections'], true) ?: [],
            'sectionOrder' => json_decode($cmsSettings['homepage_section_order'], true) ?: [],
            'settings' => $cmsSettings,
        ]);
    }
}
