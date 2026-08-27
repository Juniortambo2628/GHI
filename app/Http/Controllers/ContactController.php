<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\ContactSubmission;
use App\Models\SiteSetting;

class ContactController extends Controller
{
    public function index()
    {
        $hero = SiteSetting::grouped([
            'hero_contact_title' => 'Contact Us',
            'hero_contact_subtitle' => '',
            'hero_contact_image' => '',
            'hero_contact_button_text' => '',
            'hero_contact_button_url' => '',
        ]);

        return inertia('Contact', compact('hero'));
    }

    public function store(StoreContactRequest $request)
    {
        ContactSubmission::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'new',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you soon.',
            ]);
        }

        return back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
