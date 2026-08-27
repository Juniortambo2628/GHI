<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerRequest;
use App\Models\ContactSubmission;

class VolunteerController extends Controller
{
    public function store(StoreVolunteerRequest $request)
    {
        $parts = explode(' ', $request->name, 2);
        $contact = ContactSubmission::create([
            'firstname' => $parts[0] ?? $request->name,
            'lastname' => $parts[1] ?? '',
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => 'Volunteer Interest: ' . ($request->interest ?? 'General'),
            'message' => $request->message ?? '',
            'status' => 'new',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your interest! We will be in touch.',
                'data' => ['id' => $contact->id],
            ]);
        }

        return back()->with('success', 'Thank you for your interest! We will be in touch.');
    }
}
