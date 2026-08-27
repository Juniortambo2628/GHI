<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Cause;
use App\Models\ContactSubmission;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\NewsletterSubscriber;
use App\Models\PageView;
use App\Models\Story;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'causes' => Cause::count(),
            'initiatives' => Initiative::count(),
            'events' => Event::count(),
            'impact' => ImpactActivity::count(),
            'stories' => Story::count(),
            'contacts' => ContactSubmission::where('status', 'new')->count(),
            'contacts_total' => ContactSubmission::count(),
            'subscribers' => NewsletterSubscriber::where('status', 'active')->count(),
            'subscribers_total' => NewsletterSubscriber::count(),
            'visitors' => PageView::where('occurred_at', '>=', now()->subDays(30))->distinct('visitor_hash')->count('visitor_hash'),
            'visitors_today' => PageView::where('occurred_at', '>=', now()->startOfDay())->distinct('visitor_hash')->count('visitor_hash'),
        ];

        $recentContacts = ContactSubmission::orderByDesc('created_at')->limit(5)->get();
        $recentSubscribers = NewsletterSubscriber::orderByDesc('created_at')->limit(5)->get();

        $publishedCounts = [
            'causes' => Cause::where('status', 'published')->count(),
            'initiatives' => Initiative::where('status', 'published')->count(),
            'events' => Event::where('status', 'published')->count(),
            'stories' => Story::where('status', 'published')->count(),
            'impact' => ImpactActivity::where('status', 'published')->count(),
        ];

        $upcomingEvents = Event::where('event_date', '>=', now())
            ->where('status', 'published')
            ->orderBy('event_date')
            ->limit(3)
            ->get(['id', 'title', 'event_date', 'location']);

        $recentStories = Story::orderByDesc('created_at')
            ->limit(3)
            ->get(['id', 'title', 'author', 'status', 'created_at']);

        $topPages = PageView::selectRaw('path, COUNT(*) as views')
            ->where('occurred_at', '>=', now()->subDays(30))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(5)
            ->get();

        $visitorsByDay = PageView::selectRaw('DATE(occurred_at) as date, COUNT(DISTINCT visitor_hash) as visitors')
            ->where('occurred_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $recentNotifications = AdminNotification::forUser($request->user()->id)
            ->unread()
            ->latest()
            ->limit(5)
            ->get();

        return inertia('Admin/Dashboard', compact(
            'stats',
            'recentContacts',
            'recentSubscribers',
            'publishedCounts',
            'upcomingEvents',
            'recentStories',
            'topPages',
            'visitorsByDay',
            'recentNotifications',
        ));
    }
}
