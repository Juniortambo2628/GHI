<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        if ($request->input('upcoming')) {
            $query->upcoming();
        } elseif ($request->input('past')) {
            $query->past();
        }

        $events = $query->with('initiative')->orderBy('event_date', 'desc')->paginate(12);

        return inertia('Events', compact('events'));
    }

    public function show(Event $event)
    {
        $impactActivities = $event->impactActivities()->published()->get();

        return inertia('EventShow', compact('event', 'impactActivities'));
    }
}
