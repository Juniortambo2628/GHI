<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterRequest;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function store(StoreNewsletterRequest $request)
    {
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
