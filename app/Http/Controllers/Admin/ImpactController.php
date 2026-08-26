<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreImpactRequest;
use App\Http\Requests\Admin\UpdateImpactRequest;
use App\Models\ImpactActivity;
use App\Models\Event;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ImpactController extends Controller
{
    public function index(Request $request)
    {
        $impacts = ImpactActivity::with('event')->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))->when($request->status, fn ($q, $status) => $q->where('status', $status))->when($request->from, fn ($q, $from) => $q->whereDate('activity_date', '>=', $from))->when($request->to, fn ($q, $to) => $q->whereDate('activity_date', '<=', $to))->latest()->paginate(20)->withQueryString();
        $events = Event::published()->orderBy('event_date', 'desc')->get();
        $statusOptions = ImpactActivity::select('status')->distinct()->orderBy('status')->pluck('status')->map(fn ($s) => ['value' => $s, 'label' => ucfirst($s)])->values()->all();

        return inertia('Admin/Impact/Index', ['impacts' => $impacts, 'events' => $events, 'statusOptions' => $statusOptions, 'filters' => $request->only('search', 'status', 'from', 'to')]);
    }

    public function create()
    {
        $events = Event::published()->orderBy('event_date', 'desc')->get();

        return inertia('Admin/Impact/Create', compact('events'));
    }

    public function store(StoreImpactRequest $request)
    {
        $validated = $request->validated();

        ImpactActivity::create($validated);

        return redirect()->route('admin.impact.index')
            ->with('success', 'Impact activity created successfully.');
    }

    public function show(ImpactActivity $impact)
    {
        return inertia('Admin/Impact/Show', compact('impact'));
    }

    public function edit(ImpactActivity $impact)
    {
        $events = Event::published()->orderBy('event_date', 'desc')->get();

        return inertia('Admin/Impact/Edit', compact('impact', 'events'));
    }

    public function update(UpdateImpactRequest $request, ImpactActivity $impact)
    {
        $validated = $request->validated();

        $impact->update($validated);

        return redirect()->route('admin.impact.index')
            ->with('success', 'Impact activity updated successfully.');
    }

    public function destroy(ImpactActivity $impact)
    {
        $impact->delete();

        return redirect()->route('admin.impact.index')
            ->with('success', 'Impact activity deleted successfully.');
    }
}
