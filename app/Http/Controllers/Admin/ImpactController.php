<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreImpactRequest;
use App\Http\Requests\Admin\UpdateImpactRequest;
use App\Models\ImpactActivity;
use App\Models\Event;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ImpactController extends Controller
{
    public function index()
    {
        $impacts = ImpactActivity::with('event')->latest()->paginate(20);

        return inertia('Admin/Impact/Index', compact('impacts'));
    }

    public function create()
    {
        $events = Event::published()->orderBy('event_date', 'desc')->get();

        return inertia('Admin/Impact/Create', compact('events'));
    }

    public function store(StoreImpactRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);

        ImpactActivity::create($validated);

        return redirect()->route('admin.impact.index')
            ->with('success', 'Impact activity created successfully.');
    }

    public function show(ImpactActivity $impactActivity)
    {
        return inertia('Admin/Impact/Show', compact('impactActivity'));
    }

    public function edit(ImpactActivity $impactActivity)
    {
        $events = Event::published()->orderBy('event_date', 'desc')->get();

        return inertia('Admin/Impact/Edit', compact('impactActivity', 'events'));
    }

    public function update(UpdateImpactRequest $request, ImpactActivity $impactActivity)
    {
        $validated = $request->validated();

        $impactActivity->update($validated);

        return redirect()->route('admin.impact.index')
            ->with('success', 'Impact activity updated successfully.');
    }

    public function destroy(ImpactActivity $impactActivity)
    {
        $impactActivity->delete();

        return redirect()->route('admin.impact.index')
            ->with('success', 'Impact activity deleted successfully.');
    }
}
