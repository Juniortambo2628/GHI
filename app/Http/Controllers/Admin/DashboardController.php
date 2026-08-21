<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cause;
use App\Models\Initiative;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'causes' => Cause::count(),
            'initiatives' => Initiative::count(),
            'events' => Event::count(),
            'impact' => ImpactActivity::count(),
            'contacts' => ContactSubmission::where('status', 'new')->count(),
            'subscribers' => NewsletterSubscriber::where('status', 'active')->count(),
        ];

        $recentContacts = ContactSubmission::orderByDesc('created_at')->limit(5)->get();

        return inertia('Admin/Dashboard', compact('stats', 'recentContacts'));
    }
}
