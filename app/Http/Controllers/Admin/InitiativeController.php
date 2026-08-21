<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInitiativeRequest;
use App\Http\Requests\Admin\UpdateInitiativeRequest;
use App\Models\Initiative;
use App\Models\Cause;
use Illuminate\Support\Str;
use Inertia\Inertia;

class InitiativeController extends Controller
{
    public function index()
    {
        $initiatives = Initiative::with('cause')->latest()->paginate(20);

        return inertia('Admin/Initiatives/Index', compact('initiatives'));
    }

    public function create()
    {
        $causes = Cause::orderBy('title')->get();

        return inertia('Admin/Initiatives/Create', compact('causes'));
    }

    public function store(StoreInitiativeRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);

        Initiative::create($validated);

        return redirect()->route('admin.initiatives.index')
            ->with('success', 'Initiative created successfully.');
    }

    public function show(Initiative $initiative)
    {
        $events = $initiative->events()->paginate(10);

        return inertia('Admin/Initiatives/Show', compact('initiative', 'events'));
    }

    public function edit(Initiative $initiative)
    {
        $causes = Cause::orderBy('title')->get();

        return inertia('Admin/Initiatives/Edit', compact('initiative', 'causes'));
    }

    public function update(UpdateInitiativeRequest $request, Initiative $initiative)
    {
        $validated = $request->validated();

        $initiative->update($validated);

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
