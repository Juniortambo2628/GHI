<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HasStatusOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\Initiative;
use Inertia\Inertia;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use HasStatusOptions;
    public function index(Request $request)
    {
        $events = Event::with(['initiative', 'images'])->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))->when($request->status, fn ($q, $status) => $q->where('status', $status))->when($request->from, fn ($q, $from) => $q->whereDate('event_date', '>=', $from))->when($request->to, fn ($q, $to) => $q->whereDate('event_date', '<=', $to))->latest('event_date')->paginate(20)->withQueryString();
        $initiatives = Initiative::published()->orderBy('title')->get();
        $statusOptions = $this->getStatusOptions(Event::class);

        return inertia('Admin/Events/Index', ['events' => $events, 'initiatives' => $initiatives, 'statusOptions' => $statusOptions, 'filters' => $request->only('search', 'status', 'from', 'to')]);
    }

    public function create()
    {
        $initiatives = Initiative::published()->orderBy('title')->get();

        return inertia('Admin/Events/Create', compact('initiatives'));
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        $event = Event::create($validated);

        if ($request->has('images')) {
            $this->syncImagesInternal($event, $request->input('images'));
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $event->load('images');
        $impactActivities = $event->impactActivities()->paginate(10);

        return inertia('Admin/Events/Show', compact('event', 'impactActivities'));
    }

    public function edit(Event $event)
    {
        $event->load('images');
        $initiatives = Initiative::published()->orderBy('title')->get();

        return inertia('Admin/Events/Edit', compact('event', 'initiatives'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();

        $event->update($validated);

        if ($request->has('images')) {
            $this->syncImagesInternal($event, $request->input('images'));
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    private function syncImagesInternal(Event $event, array $images)
    {
        $incoming = collect($images);
        $incomingPaths = $incoming->pluck('path')->toArray();

        $event->images()->whereNotIn('path', $incomingPaths)->delete();

        foreach ($incoming as $index => $item) {
            $existing = EventImage::where('event_id', $event->id)->where('path', $item['path'])->first();
            if ($existing) {
                $existing->update(['sort_order' => $index, 'type' => $item['type'] ?? 'image']);
            } else {
                EventImage::create([
                    'event_id' => $event->id,
                    'path' => $item['path'],
                    'type' => $item['type'] ?? 'image',
                    'sort_order' => $index,
                ]);
            }
        }
    }

    public function syncImages(Request $request, Event $event)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*.path' => 'required|string|max:255',
            'images.*.sort_order' => 'required|integer|min:0',
            'images.*.type' => 'nullable|string|in:image,video',
            'images.*.id' => 'nullable|integer|exists:event_images,id',
        ]);

        $this->syncImagesInternal($event, $validated['images']);

        return back()->with('success', 'Gallery images updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
