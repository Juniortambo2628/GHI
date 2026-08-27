<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GetInvolvedSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GetInvolvedSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $submissions = GetInvolvedSubmission::query()
            ->with('initiative')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('full_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->latest('created_at')->paginate(15)->withQueryString();

        return Inertia::render('Admin/GetInvolved/Index', [
            'submissions' => $submissions,
            'filters' => $request->only('status', 'search'),
        ]);
    }

    public function show($id)
    {
        $submission = GetInvolvedSubmission::with('initiative')->findOrFail($id);

        return Inertia::render('Admin/GetInvolved/Show', ['submission' => $submission]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $submission = GetInvolvedSubmission::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:new,reviewed,contacted,closed',
        ]);

        $submission->update($validated);

        return back()->with('success', 'Submission status updated.');
    }

    public function destroy($id): RedirectResponse
    {
        GetInvolvedSubmission::findOrFail($id)->delete();

        return back()->with('success', 'Submission deleted.');
    }
}
