<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|max:255',
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

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'status' => 'active',
                'subscribed_at' => now(),
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for subscribing!',
                'data' => [
                    'id' => $subscriber->id,
                    'email' => $subscriber->email,
                ],
            ]);
        }

        return back()->with('success', 'Thank you for subscribing!');
    }
}
