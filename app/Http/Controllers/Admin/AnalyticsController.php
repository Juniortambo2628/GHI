<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cause;
use App\Models\ContactSubmission;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\NewsletterSubscriber;
use App\Models\PageView;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(29)->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        $views = PageView::whereBetween('occurred_at', [$from, $to])
            ->selectRaw('DATE(occurred_at) as date, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
            ->groupBy('date')->orderBy('date')->get();

        $totalViews = PageView::whereBetween('occurred_at', [$from, $to])->count();
        $totalVisitors = PageView::whereBetween('occurred_at', [$from, $to])->distinct('visitor_hash')->count('visitor_hash');
        $totalContacts = ContactSubmission::whereBetween('created_at', [$from, $to])->count();
        $totalSubscribers = NewsletterSubscriber::whereBetween('created_at', [$from, $to])->count();

        $prevFrom = (clone $from)->subDays($from->diffInDays($to));
        $prevTo = (clone $to)->subDays($from->diffInDays($to));
        $prevViews = PageView::whereBetween('occurred_at', [$prevFrom, $prevTo])->count();
        $prevVisitors = PageView::whereBetween('occurred_at', [$prevFrom, $prevTo])->distinct('visitor_hash')->count('visitor_hash');

        $contentBreakdown = [
            'causes' => Cause::count(),
            'initiatives' => Initiative::count(),
            'events' => Event::count(),
            'stories' => Story::count(),
            'impact' => ImpactActivity::count(),
        ];

        $topPages = PageView::whereBetween('occurred_at', [$from, $to])
            ->select('path', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_hash) as visitors'))
            ->groupBy('path')->orderByDesc('views')->limit(10)->get();

        $dayExpr = DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%w', occurred_at) AS INTEGER) + 1"
            : 'DAYOFWEEK(occurred_at)';
        $trafficByDay = PageView::whereBetween('occurred_at', [$from, $to])
            ->selectRaw("{$dayExpr} as day, COUNT(*) as views")
            ->groupBy('day')->orderBy('day')->get();

        $viewGrowth = $prevViews > 0 ? round(($totalViews - $prevViews) / $prevViews * 100, 1) : 0;
        $visitorGrowth = $prevVisitors > 0 ? round(($totalVisitors - $prevVisitors) / $prevVisitors * 100, 1) : 0;
        $engagement = $totalVisitors > 0 ? round($totalViews / $totalVisitors, 1) : 0;
        $conversionRate = $totalVisitors > 0 ? round($totalContacts / $totalVisitors * 100, 1) : 0;

        return inertia('Admin/Analytics/Index', [
            'metrics' => [
                'content' => array_sum($contentBreakdown),
                'views' => $totalViews,
                'visitors' => $totalVisitors,
                'contacts' => $totalContacts,
                'subscribers' => $totalSubscribers,
            ],
            'advancedMetrics' => [
                'viewGrowth' => $viewGrowth,
                'visitorGrowth' => $visitorGrowth,
                'engagement' => $engagement,
                'conversionRate' => $conversionRate,
            ],
            'contentBreakdown' => $contentBreakdown,
            'topPages' => $topPages,
            'trafficByDay' => $trafficByDay,
            'views' => $views,
            'filters' => $request->only('from', 'to'),
        ]);
    }

    public function report(Request $request)
    {
        $from = $request->date('from')?->startOfDay() ?? now()->subDays(29)->startOfDay();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();
        $type = $request->get('type', 'overview');

        $data = match ($type) {
            'traffic' => $this->trafficReport($from, $to),
            'content' => $this->contentReport($from, $to),
            default => $this->overviewReport($from, $to),
        };

        return inertia('Admin/Analytics/Reports', [
            'report' => $data,
            'type' => $type,
            'filters' => $request->only('from', 'to'),
        ]);
    }

    private function overviewReport($from, $to)
    {
        return [
            'title' => 'Overview Report',
            'summary' => [
                'total_views' => PageView::whereBetween('occurred_at', [$from, $to])->count(),
                'unique_visitors' => PageView::whereBetween('occurred_at', [$from, $to])->distinct('visitor_hash')->count('visitor_hash'),
                'total_contacts' => ContactSubmission::whereBetween('created_at', [$from, $to])->count(),
                'total_subscribers' => NewsletterSubscriber::whereBetween('created_at', [$from, $to])->count(),
            ],
            'daily' => PageView::whereBetween('occurred_at', [$from, $to])
                ->selectRaw('DATE(occurred_at) as date, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
                ->groupBy('date')->orderBy('date')->get(),
        ];
    }

    private function trafficReport($from, $to)
    {
        $hourExpr = DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%H', occurred_at) AS INTEGER)"
            : 'HOUR(occurred_at)';

        return [
            'title' => 'Traffic Report',
            'daily' => PageView::whereBetween('occurred_at', [$from, $to])
                ->selectRaw('DATE(occurred_at) as date, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
                ->groupBy('date')->orderBy('date')->get(),
            'top_pages' => PageView::whereBetween('occurred_at', [$from, $to])
                ->select('path', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_hash) as visitors'))
                ->groupBy('path')->orderByDesc('views')->limit(20)->get(),
            'by_hour' => PageView::whereBetween('occurred_at', [$from, $to])
                ->selectRaw("{$hourExpr} as hour, COUNT(*) as views")
                ->groupBy('hour')->orderBy('hour')->get(),
        ];
    }

    private function contentReport($from, $to)
    {
        return [
            'title' => 'Content Report',
            'counts' => [
                'causes' => Cause::count(),
                'initiatives' => Initiative::count(),
                'events' => Event::whereBetween('created_at', [$from, $to])->count(),
                'stories' => Story::whereBetween('created_at', [$from, $to])->count(),
                'impact' => ImpactActivity::whereBetween('created_at', [$from, $to])->count(),
            ],
            'recent_content' => [
                'stories' => Story::whereBetween('created_at', [$from, $to])->latest()->limit(10)->get(['id', 'title', 'created_at', 'status']),
                'events' => Event::whereBetween('created_at', [$from, $to])->latest()->limit(10)->get(['id', 'title', 'created_at', 'status']),
                'impact' => ImpactActivity::whereBetween('created_at', [$from, $to])->latest()->limit(10)->get(['id', 'title', 'created_at', 'status']),
            ],
        ];
    }
}
