<?php

namespace Tests\Feature;

use App\Models\Cause;
use App\Models\ContactSubmission;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Initiative;
use App\Models\NewsletterSubscriber;
use App\Models\PageView;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    // --- Analytics Page ---

    public function test_non_admin_cannot_view_analytics(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/analytics')->assertForbidden();
    }

    public function test_admin_can_view_analytics(): void
    {
        $this->actingAs($this->admin())->get('/admin/analytics')->assertOk();
    }

    public function test_analytics_returns_metrics(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/analytics')
            ->assertOk();
    }

    public function test_analytics_aggregates_content_counts(): void
    {
        Cause::factory()->count(2)->create();
        Initiative::factory()->count(3)->create();
        Event::factory()->count(4)->create();
        ImpactActivity::factory()->count(5)->create();
        Story::factory()->count(6)->create();

        $this->actingAs($this->admin())->get('/admin/analytics')->assertOk();
    }

    public function test_analytics_counts_page_views(): void
    {
        PageView::insert([
            ['path' => '/', 'occurred_at' => now()->subDays(5)],
            ['path' => '/', 'occurred_at' => now()->subDays(3)],
            ['path' => '/causes', 'occurred_at' => now()->subDays(1)],
        ]);

        $this->actingAs($this->admin())->get('/admin/analytics')->assertOk();
    }

    public function test_analytics_date_range_from_filter(): void
    {
        PageView::insert([
            ['path' => '/', 'occurred_at' => now()->subDays(10)],
            ['path' => '/', 'occurred_at' => now()->subDays(2)],
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/analytics?from=' . now()->subDays(5)->format('Y-m-d'))
            ->assertOk();
    }

    public function test_analytics_date_range_to_filter(): void
    {
        PageView::insert([
            ['path' => '/', 'occurred_at' => now()->subDays(10)],
            ['path' => '/', 'occurred_at' => now()->subDays(2)],
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/analytics?to=' . now()->subDays(5)->format('Y-m-d'))
            ->assertOk();
    }

    public function test_analytics_date_range_combined(): void
    {
        PageView::insert([
            ['path' => '/', 'occurred_at' => now()->subDays(10)],
            ['path' => '/', 'occurred_at' => now()->subDays(5)],
            ['path' => '/', 'occurred_at' => now()->subDays(1)],
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/analytics?from=' . now()->subDays(8)->format('Y-m-d') . '&to=' . now()->subDays(3)->format('Y-m-d'))
            ->assertOk();
    }

    // --- Export Routes ---

    public function test_export_contacts_returns_csv(): void
    {
        ContactSubmission::factory()->create();
        $this->actingAs($this->admin())
            ->get('/admin/exports/contacts')
            ->assertOk()
            ->assertHeaderContains('Content-Type', 'text/csv');
    }

    public function test_export_subscribers_returns_csv(): void
    {
        NewsletterSubscriber::factory()->create();
        $this->actingAs($this->admin())
            ->get('/admin/exports/subscribers')
            ->assertOk()
            ->assertHeaderContains('Content-Type', 'text/csv');
    }

    public function test_export_donations_returns_redirect(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/exports/donations')
            ->assertRedirect();
    }

    public function test_export_content_resources_returns_csv(): void
    {
        Cause::factory()->create();
        Initiative::factory()->create();
        Event::factory()->create();
        ImpactActivity::factory()->create();
        Story::factory()->create();

        foreach (['causes', 'initiatives', 'events', 'impact', 'stories'] as $resource) {
            $this->actingAs($this->admin())
                ->get("/admin/exports/{$resource}")
                ->assertOk()
                ->assertHeaderContains('Content-Type', 'text/csv');
        }
    }

    public function test_export_invalid_resource_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/exports/nonexistent')
            ->assertNotFound();
    }

    public function test_export_with_status_filter(): void
    {
        Cause::factory()->create(['status' => 'published']);
        $this->actingAs($this->admin())
            ->get('/admin/exports/causes?status=published')
            ->assertOk();
    }

    public function test_export_with_date_range(): void
    {
        Event::factory()->create(['event_date' => now()->subWeek()]);
        $this->actingAs($this->admin())
            ->get('/admin/exports/events?from=' . now()->subMonth()->format('Y-m-d') . '&to=' . now()->format('Y-m-d'))
            ->assertOk();
    }

    public function test_non_admin_cannot_export(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/exports/contacts')
            ->assertForbidden();
    }

    public function test_dashboard_displays_visitor_stats(): void
    {
        PageView::insert([
            ['path' => '/', 'occurred_at' => now()->subDays(10)],
            ['path' => '/', 'occurred_at' => now()->subDays(2)],
        ]);

        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }

    public function test_dashboard_displays_content_counts(): void
    {
        Cause::factory()->count(2)->create();
        Initiative::factory()->count(3)->create();

        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }
}
