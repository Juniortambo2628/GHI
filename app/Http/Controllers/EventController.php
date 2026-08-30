<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::published();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($location = $request->input('location')) {
            $query->where('location', 'like', "%{$location}%");
        }

        if ($request->input('upcoming')) {
            $query->upcoming()->orderBy('event_date', 'asc');
        } elseif ($request->input('past')) {
            $query->past()->orderBy('event_date', 'desc');
        } else {
            // "Most current events first" - sort by event_date desc by default
            $query->orderBy('event_date', 'desc');
        }

        $events = $query->with(['initiative', 'images'])->paginate(12);

        $hero = SiteSetting::grouped([
            'hero_events_title' => 'Events & Activities',
            'hero_events_subtitle' => '',
            'hero_events_image' => '',
            'hero_events_button_text' => '',
            'hero_events_button_url' => '',
        ]);

        return inertia('Events', compact('events', 'hero'));
    }

    public function show(Event $event)
    {
        $event->load(['images', 'initiative']);
        $impactActivities = $event->impactActivities()->published()->get();

        return inertia('EventShow', compact('event', 'impactActivities'));
    }
}
