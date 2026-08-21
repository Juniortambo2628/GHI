<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'interest' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator);
        }

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
