<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInitiativeRequest;
use App\Http\Requests\Admin\UpdateInitiativeRequest;
use App\Models\Initiative;
use App\Models\Cause;
use Inertia\Inertia;
use Illuminate\Http\Request;

class InitiativeController extends Controller
{
    public function index(Request $request)
    {
        $initiatives = Initiative::with('causes')->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))->when($request->status, fn ($q, $status) => $q->where('status', $status))->when($request->category, fn ($q, $category) => $q->where('category', $category))->latest()->paginate(20)->withQueryString();
        $causes = Cause::orderBy('title')->get();
        $statusOptions = Initiative::select('status')->distinct()->orderBy('status')->pluck('status')->map(fn ($s) => ['value' => $s, 'label' => ucfirst($s)])->values()->all();
        $categoryOptions = Initiative::select('category')->distinct()->whereNotNull('category')->orderBy('category')->pluck('category')->map(fn ($c) => ['value' => $c, 'label' => ucfirst($c)])->values()->all();

        return inertia('Admin/Initiatives/Index', ['initiatives' => $initiatives, 'causes' => $causes, 'statusOptions' => $statusOptions, 'categoryOptions' => $categoryOptions, 'filters' => $request->only('search', 'status', 'category')]);
    }

    public function create()
    {
        $causes = Cause::orderBy('title')->get();

        return inertia('Admin/Initiatives/Create', compact('causes'));
    }

    public function store(StoreInitiativeRequest $request)
    {
        $validated = $request->validated();
        $causeIds = $validated['cause_ids'] ?? [];
        unset($validated['cause_ids']);

        $initiative = Initiative::create($validated);
        $initiative->causes()->sync($causeIds);

        return redirect()->route('admin.initiatives.index')
            ->with('success', 'Initiative created successfully.');
    }

    public function show(Initiative $initiative)
    {
        $initiative->load('causes');
        $events = $initiative->events()->paginate(10);

        return inertia('Admin/Initiatives/Show', compact('initiative', 'events'));
    }

    public function edit(Initiative $initiative)
    {
        $initiative->load('causes');
        $causes = Cause::orderBy('title')->get();

        return inertia('Admin/Initiatives/Edit', compact('initiative', 'causes'));
    }

    public function update(UpdateInitiativeRequest $request, Initiative $initiative)
    {
        $validated = $request->validated();
        $causeIds = $validated['cause_ids'] ?? [];
        unset($validated['cause_ids']);

        $initiative->update($validated);
        $initiative->causes()->sync($causeIds);

        return redirect()->route('admin.initiatives.index')
            ->with('success', 'Initiative updated successfully.');
    }

    public function destroy(Initiative $initiative)
    {
        $initiative->delete();

        return redirect()->route('admin.initiatives.index')
            ->with('success', 'Initiative deleted successfully.');
    }
}
