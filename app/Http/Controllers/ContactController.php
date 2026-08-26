<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|min:2|max:255',
            'lastname' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|min:5|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

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
