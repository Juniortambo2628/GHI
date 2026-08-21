<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonateController extends Controller
{
    public function index()
    {
        return inertia('Donate');
    }

    public function success()
    {
        return inertia('DonateSuccess');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'amount' => 'required|numeric|min:1',
            'donation_type' => 'required|in:one-time,monthly',
        ]);

        $donation = Donation::create([
            ...$validated,
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        // In a real app, redirect to Stripe Checkout here
        return redirect()->route('donate.success')
            ->with('donation', $donation);
    }
}
