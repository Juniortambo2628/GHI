<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use App\Models\Initiative;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('initiative')->latest('event_date')->paginate(20);

        return inertia('Admin/Events/Index', compact('events'));
    }

    public function create()
    {
        $initiatives = Initiative::published()->orderBy('title')->get();

        return inertia('Admin/Events/Create', compact('initiatives'));
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $impactActivities = $event->impactActivities()->paginate(10);

        return inertia('Admin/Events/Show', compact('event', 'impactActivities'));
    }

    public function edit(Event $event)
    {
        $initiatives = Initiative::published()->orderBy('title')->get();

        return inertia('Admin/Events/Edit', compact('event', 'initiatives'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
