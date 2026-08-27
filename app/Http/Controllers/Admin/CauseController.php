<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HasStatusOptions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCauseRequest;
use App\Http\Requests\Admin\UpdateCauseRequest;
use App\Models\Cause;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CauseController extends Controller
{
    use HasStatusOptions;
    public function index(Request $request)
    {
        $causes = Cause::query()->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))->when($request->status, fn ($q, $status) => $q->where('status', $status))->orderBy('display_order')->paginate(20)->withQueryString();
        $statusOptions = $this->getStatusOptions(Cause::class);

        return inertia('Admin/Causes/Index', ['causes' => $causes, 'statusOptions' => $statusOptions, 'filters' => $request->only('search', 'status')]);
    }

    public function create()
    {
        return inertia('Admin/Causes/Create');
    }

    public function store(StoreCauseRequest $request)
    {
        $validated = $request->validated();

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
