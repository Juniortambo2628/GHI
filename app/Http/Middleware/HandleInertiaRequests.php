<?php

namespace App\Http\Middleware;

use App\Models\Cause;
use App\Models\ContactSubmission;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\NewsletterSubscriber;
use App\Models\PageView;
use App\Models\SiteSetting;
use App\Models\Story;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],
            'app_name' => config('app.name'),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'site_settings' => fn () => SiteSetting::grouped([
                'site_name' => config('app.name'),
                'site_tagline' => 'Bridging Global Compassion with Local Action',
                'site_description' => 'Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development.',
                'site_logo' => '/Logo/Square-White-BG.png',
                'site_favicon' => '/Logo/Square-White-BG.png',
                'contact_email' => 'info@globalharmonyinitiative.com',
                'contact_phone' => '+1 (437) 297-7977',
                'facebook_url' => '',
                'instagram_url' => '',
                'twitter_url' => '',
                'linkedin_url' => '',
                'footer_text' => 'Bridging Global Compassion with Local Action.',
            ]),
            'admin_stats' => function () use ($request) {
                if (! $request->user()?->is_admin) {
                    return null;
                }

                return [
                    'causes' => Cause::count(),
                    'initiatives' => Initiative::count(),
                    'events' => Event::count(),
                    'stories' => Story::count(),
                    'impact' => ImpactActivity::count(),
                    'contacts' => ContactSubmission::where('status', 'new')->count(),
                    'subscribers' => NewsletterSubscriber::where('status', 'active')->count(),
                    'visitors' => PageView::where('occurred_at', '>=', now()->subDays(30))->distinct('visitor_hash')->count('visitor_hash'),
                ];
            },
        ];
    }
}
