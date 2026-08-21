<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCauseRequest;
use App\Http\Requests\Admin\UpdateCauseRequest;
use App\Models\Cause;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CauseController extends Controller
{
    public function index()
    {
        $causes = Cause::orderBy('display_order')->paginate(20);

        return inertia('Admin/Causes/Index', compact('causes'));
    }

    public function create()
    {
        return inertia('Admin/Causes/Create');
    }

    public function store(StoreCauseRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['title']);

        Cause::create($validated);

        return redirect()->route('admin.causes.index')
            ->with('success', 'Cause created successfully.');
    }

    public function show(Cause $cause)
    {
        $initiatives = $cause->initiatives()->paginate(10);

        return inertia('Admin/Causes/Show', compact('cause', 'initiatives'));
    }

    public function edit(Cause $cause)
    {
        return inertia('Admin/Causes/Edit', compact('cause'));
    }

    public function update(UpdateCauseRequest $request, Cause $cause)
    {
        $validated = $request->validated();

        $cause->update($validated);

        return redirect()->route('admin.causes.index')
            ->with('success', 'Cause updated successfully.');
    }

    public function destroy(Cause $cause)
    {
        $cause->delete();

        return redirect()->route('admin.causes.index')
            ->with('success', 'Cause deleted successfully.');
    }
}
