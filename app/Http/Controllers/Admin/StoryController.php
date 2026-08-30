<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HasStatusOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoryRequest;
use App\Http\Requests\Admin\UpdateStoryRequest;
use App\Models\Story;
use App\Models\Event;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    use HasStatusOptions;

    public function index(Request $request)
    {
        $stories = Story::when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))->when($request->status, fn ($q, $status) => $q->where('status', $status))->when($request->category, fn ($q, $category) => $q->where('category', $category))->latest()->paginate(20)->withQueryString();
        $statusOptions = $this->getStatusOptions(Story::class);

        return inertia('Admin/Stories/Index', ['stories' => $stories, 'statusOptions' => $statusOptions, 'filters' => $request->only('search', 'status', 'category')]);
    }

    public function create()
    {
        $events = Event::orderBy('title')->get(['id', 'title', 'slug']);
        return inertia('Admin/Stories/Create', compact('events'));
    }

    public function store(StoreStoryRequest $request)
    {
        $validated = $request->validated();

        Story::create($validated);

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story created successfully.');
    }

    public function show(Story $story)
    {
        return inertia('Admin/Stories/Show', compact('story'));
    }

    public function edit(Story $story)
    {
        $events = Event::orderBy('title')->get(['id', 'title', 'slug']);
        return inertia('Admin/Stories/Edit', compact('story', 'events'));
    }

    public function update(UpdateStoryRequest $request, Story $story)
    {
        $validated = $request->validated();

        $story->update($validated);

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story updated successfully.');
    }

    public function destroy(Story $story)
    {
        $story->delete();

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story deleted successfully.');
    }
}
