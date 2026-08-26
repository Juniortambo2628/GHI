<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactSubmissionRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $contacts = ContactSubmission::query()
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('email', 'like', "%{$search}%")->orWhere('firstname', 'like', "%{$search}%")->orWhere('lastname', 'like', "%{$search}%")))
            ->latest('created_at')->paginate(15)->withQueryString();

        return inertia('Admin/Contacts/Index', ['contacts' => $contacts, 'filters' => $request->only('status', 'search')]);
    }

    public function show(ContactSubmission $contactSubmission)
    {
        return inertia('Admin/Contacts/Show', ['contact' => $contactSubmission]);
    }

    public function update(UpdateContactSubmissionRequest $request, ContactSubmission $contactSubmission): RedirectResponse
    {
        $contactSubmission->update($request->validated());

        return back()->with('success', 'Contact status updated.');
    }

    public function destroy(ContactSubmission $contactSubmission): RedirectResponse
    {
        $contactSubmission->delete();

        return back()->with('success', 'Contact deleted.');
    }
}
