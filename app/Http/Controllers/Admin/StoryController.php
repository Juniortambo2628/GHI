<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStoryRequest;
use App\Http\Requests\Admin\UpdateStoryRequest;
use App\Models\Story;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::latest()->paginate(20);

        return inertia('Admin/Stories/Index', compact('stories'));
    }

    public function create()
    {
        return inertia('Admin/Stories/Create');
    }

    public function store(StoreStoryRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);

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
        return inertia('Admin/Stories/Edit', compact('story'));
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
