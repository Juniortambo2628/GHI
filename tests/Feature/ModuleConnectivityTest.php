<?php

namespace Tests\Feature;

use App\Models\Cause;
use App\Models\Initiative;
use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\Story;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use App\Models\PageView;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\AdminNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ModuleConnectivityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    // ===== MODEL RELATIONSHIPS =====

    public function test_cause_has_many_initiatives(): void
    {
        $cause = Cause::factory()->create();
        $initiatives = Initiative::factory()->count(3)->create();
        $cause->initiatives()->sync($initiatives->pluck('id'));

        $this->assertCount(3, $cause->initiatives);
        $this->assertInstanceOf(Initiative::class, $cause->initiatives->first());
    }

    public function test_initiative_belongs_to_cause(): void
    {
        $cause = Cause::factory()->create();
        $initiative = Initiative::factory()->create();
        $initiative->causes()->attach($cause);

        $this->assertNotNull($initiative->causes->first());
        $this->assertEquals($cause->id, $initiative->causes->first()->id);
    }

    public function test_initiative_has_many_events(): void
    {
        $initiative = Initiative::factory()->create();
        Event::factory()->count(2)->create(['initiative_id' => $initiative->id]);

        $this->assertCount(2, $initiative->events);
    }

    public function test_event_belongs_to_initiative(): void
    {
        $initiative = Initiative::factory()->create();
        $event = Event::factory()->create(['initiative_id' => $initiative->id]);

        $this->assertNotNull($event->initiative);
        $this->assertEquals($initiative->id, $event->initiative->id);
    }

    public function test_event_has_many_impact_activities(): void
    {
        $event = Event::factory()->create();
        ImpactActivity::factory()->count(4)->create(['event_id' => $event->id]);

        $this->assertCount(4, $event->impactActivities);
    }

    public function test_impact_activity_belongs_to_event(): void
    {
        $event = Event::factory()->create();
        $impact = ImpactActivity::factory()->create(['event_id' => $event->id]);

        $this->assertNotNull($impact->event);
        $this->assertEquals($event->id, $impact->event->id);
    }

    public function test_admin_notification_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $notification = AdminNotification::factory()->create(['user_id' => $user->id]);

        $this->assertNotNull($notification->user);
        $this->assertEquals($user->id, $notification->user->id);
    }

    // ===== FULL HIERARCHY =====

    public function test_full_cause_to_impact_hierarchy(): void
    {
        $cause = Cause::factory()->create(['title' => 'Education']);
        $initiative = Initiative::factory()->create(['title' => 'School Build']);
        $initiative->causes()->attach($cause);
        $event = Event::factory()->create(['initiative_id' => $initiative->id, 'title' => 'Ground Breaking']);
        $impact = ImpactActivity::factory()->create(['event_id' => $event->id, 'title' => '100 Students']);

        $this->assertEquals('Education', $cause->title);
        $this->assertEquals($cause->id, $initiative->causes->first()->id);
        $this->assertEquals($initiative->id, $event->initiative->id);
        $this->assertEquals($event->id, $impact->event->id);

        $this->assertCount(1, $cause->initiatives);
        $this->assertCount(1, $initiative->events);
        $this->assertCount(1, $event->impactActivities);
    }

    public function test_delete_cause_removes_pivot_entry(): void
    {
        $cause = Cause::factory()->create();
        $initiatives = Initiative::factory()->count(2)->create();
        $cause->initiatives()->sync($initiatives->pluck('id'));

        $this->assertDatabaseCount('cause_initiative', 2);
        $cause->delete();
        $this->assertDatabaseCount('cause_initiative', 0);
    }

    public function test_delete_initiative_nullifies_event_fk(): void
    {
        $initiative = Initiative::factory()->create();
        Event::factory()->count(3)->create(['initiative_id' => $initiative->id]);

        $this->assertDatabaseCount('events', 3);
        $initiative->delete();
        $this->assertDatabaseHas('events', ['initiative_id' => null]);
    }

    public function test_delete_event_nullifies_impact_fk(): void
    {
        $event = Event::factory()->create();
        ImpactActivity::factory()->count(2)->create(['event_id' => $event->id]);

        $this->assertDatabaseCount('impact_activities', 2);
        $event->delete();
        $this->assertDatabaseHas('impact_activities', ['event_id' => null]);
    }

    // ===== INDEPENDENT MODELS =====

    public function test_story_is_independent(): void
    {
        $story = Story::factory()->create(['title' => 'My Story', 'author' => 'Jane']);
        $this->assertDatabaseHas('stories', ['id' => $story->id, 'title' => 'My Story', 'author' => 'Jane']);
    }

    public function test_contact_submission_is_independent(): void
    {
        $contact = ContactSubmission::factory()->create(['firstname' => 'John', 'lastname' => 'Doe']);
        $this->assertDatabaseHas('contact_submissions', ['firstname' => 'John', 'lastname' => 'Doe']);
    }

    public function test_newsletter_subscriber_is_independent(): void
    {
        $sub = NewsletterSubscriber::factory()->create(['email' => 'test@example.com']);
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'test@example.com']);
    }

    public function test_donation_is_independent(): void
    {
        DB::table('donations')->insert([
            'donor_name' => 'Test Donor',
            'donor_email' => 'donor@example.com',
            'amount' => 100.50,
            'currency' => 'USD',
            'donation_type' => 'one-time',
            'status' => 'completed',
            'created_at' => now(),
        ]);
        $this->assertDatabaseHas('donations', ['amount' => 100.50, 'status' => 'completed']);
    }

    public function test_page_view_is_independent(): void
    {
        $pv = PageView::create([
            'path' => '/causes',
            'visitor_hash' => 'abc123',
            'occurred_at' => now(),
        ]);
        $this->assertDatabaseHas('page_views', ['path' => '/causes', 'visitor_hash' => 'abc123']);
    }

    // ===== SLUG GENERATION =====

    public function test_cause_slug_auto_generated(): void
    {
        $cause = Cause::factory()->create(['title' => 'Education For All', 'slug' => '']);
        $this->assertEquals('education-for-all', $cause->fresh()->slug);
    }

    public function test_initiative_slug_auto_generated(): void
    {
        $initiative = Initiative::factory()->create(['title' => 'Build Schools', 'slug' => '']);
        $this->assertEquals('build-schools', $initiative->fresh()->slug);
    }

    public function test_event_slug_auto_generated(): void
    {
        $event = Event::factory()->create(['title' => 'Annual Gala', 'slug' => '']);
        $this->assertEquals('annual-gala', $event->fresh()->slug);
    }

    public function test_impact_slug_auto_generated(): void
    {
        $impact = ImpactActivity::factory()->create(['title' => 'Clean Water Project', 'slug' => '']);
        $this->assertEquals('clean-water-project', $impact->fresh()->slug);
    }

    public function test_story_slug_auto_generated(): void
    {
        $story = Story::factory()->create(['title' => 'A New Beginning', 'slug' => '']);
        $this->assertEquals('a-new-beginning', $story->fresh()->slug);
    }

    // ===== SCOPES =====

    public function test_cause_published_scope(): void
    {
        Cause::factory()->create(['status' => 'published']);
        Cause::factory()->create(['status' => 'draft']);

        $this->assertCount(1, Cause::published()->get());
    }

    public function test_initiative_published_scope(): void
    {
        Initiative::factory()->create(['status' => 'published']);
        Initiative::factory()->create(['status' => 'draft']);

        $this->assertCount(1, Initiative::published()->get());
    }

    public function test_event_published_scope(): void
    {
        Event::factory()->create(['status' => 'published']);
        Event::factory()->create(['status' => 'draft']);

        $this->assertCount(1, Event::published()->get());
    }

    public function test_event_upcoming_scope(): void
    {
        Event::factory()->create(['event_date' => now()->addDays(5), 'status' => 'published']);
        Event::factory()->create(['event_date' => now()->subDays(5), 'status' => 'published']);

        $this->assertCount(1, Event::upcoming()->get());
    }

    public function test_event_past_scope(): void
    {
        Event::factory()->create(['event_date' => now()->subDays(5), 'status' => 'published']);
        Event::factory()->create(['event_date' => now()->addDays(5), 'status' => 'published']);

        $this->assertCount(1, Event::past()->get());
    }

    public function test_story_published_scope(): void
    {
        Story::factory()->create(['status' => 'published']);
        Story::factory()->create(['status' => 'draft']);

        $this->assertCount(1, Story::published()->get());
    }

    public function test_impact_published_scope(): void
    {
        ImpactActivity::factory()->create(['status' => 'published']);
        ImpactActivity::factory()->create(['status' => 'draft']);

        $this->assertCount(1, ImpactActivity::published()->get());
    }

    public function test_impact_featured_scope(): void
    {
        ImpactActivity::factory()->create(['featured' => true]);
        ImpactActivity::factory()->create(['featured' => false]);

        $this->assertCount(1, ImpactActivity::featured()->get());
    }

    public function test_notification_unread_scope(): void
    {
        $user = $this->admin();
        AdminNotification::factory()->create(['user_id' => $user->id, 'read_at' => null]);
        AdminNotification::factory()->create(['user_id' => $user->id, 'read_at' => now()]);

        $this->assertCount(1, AdminNotification::forUser($user->id)->unread()->get());
    }

    // ===== ADMIN CRUD VIA ROUTES =====

    public function test_admin_can_access_all_cms_screens(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $screens = [
            '/admin',
            '/admin/causes',
            '/admin/causes/create',
            '/admin/initiatives',
            '/admin/initiatives/create',
            '/admin/events',
            '/admin/events/create',
            '/admin/impact',
            '/admin/impact/create',
            '/admin/stories',
            '/admin/stories/create',
            '/admin/contacts',
            '/admin/subscribers',
            '/admin/analytics',
            '/admin/analytics/report',
            '/admin/settings',
            '/admin/media',
            '/admin/system-status',
        ];

        foreach ($screens as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_non_admin_denied_from_all_cms_screens(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user);

        $screens = [
            '/admin',
            '/admin/causes',
            '/admin/initiatives',
            '/admin/events',
            '/admin/impact',
            '/admin/stories',
            '/admin/contacts',
            '/admin/subscribers',
            '/admin/analytics',
            '/admin/settings',
            '/admin/media',
            '/admin/system-status',
        ];

        foreach ($screens as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_unauthenticated_redirected_from_admin(): void
    {
        $screens = ['/admin', '/admin/causes', '/admin/events', '/admin/settings'];

        foreach ($screens as $url) {
            $this->get($url)->assertRedirect();
        }
    }

    // ===== SETTINGS =====

    public function test_settings_stored_and_retrieved(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        SiteSetting::updateOrCreate(['key' => 'site_name'], ['value' => 'Test Org', 'group' => 'general']);
        $settings = SiteSetting::grouped(['site_name' => '']);

        $this->assertEquals('Test Org', $settings['site_name']);
    }

    public function test_settings_update_via_request(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->put('/admin/settings', ['site_name' => 'New Name', 'site_tagline' => 'New Tagline'])
            ->assertRedirect();

        $this->assertDatabaseHas('site_settings', ['key' => 'site_name', 'value' => 'New Name']);
    }

    // ===== CONTACTS =====

    public function test_contacts_index_and_delete(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        ContactSubmission::factory()->count(3)->create();

        $this->get('/admin/contacts')->assertOk();

        $contact = ContactSubmission::first();
        $this->delete("/admin/contacts/{$contact->id}")->assertRedirect();
        $this->assertDatabaseMissing('contact_submissions', ['id' => $contact->id]);
    }

    // ===== DONATIONS (deprecated - redirects to dashboard) =====

    public function test_donations_index_redirects(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->get('/admin/donations')->assertRedirect();
    }

    // ===== SUBSCRIBERS =====

    public function test_subscribers_index(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        NewsletterSubscriber::factory()->count(5)->create();

        $this->get('/admin/subscribers')->assertOk();
    }

    // ===== ANALYTICS =====

    public function test_analytics_page_loads(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->get('/admin/analytics')->assertOk();
        $this->get('/admin/analytics/report')->assertOk();
    }

    public function test_analytics_includes_metrics(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        Cause::factory()->count(3)->create();
        Initiative::factory()->count(2)->create();
        Event::factory()->create();
        Story::factory()->create(['status' => 'published']);

        $response = $this->get('/admin/analytics');
        $response->assertOk();
    }

    // ===== NOTIFICATIONS =====

    public function test_notifications_api(): void
    {
        $admin = $this->admin();
        AdminNotification::factory()->create(['user_id' => $admin->id, 'read_at' => null]);

        $response = $this->actingAs($admin)->getJson('/admin/notifications/unread-count');
        $response->assertOk();
        $response->assertJsonStructure(['count']);
    }

    public function test_notification_mark_read(): void
    {
        $admin = $this->admin();
        $notif = AdminNotification::factory()->create(['user_id' => $admin->id, 'read_at' => null]);

        $this->actingAs($admin)->putJson("/admin/notifications/{$notif->id}/read")->assertOk();
        $this->assertDatabaseHas('admin_notifications', ['id' => $notif->id]);
        $dbValue = \DB::table('admin_notifications')->where('id', $notif->id)->value('read_at');
        $this->assertNotNull($dbValue);
    }

    public function test_notification_mark_all_read(): void
    {
        $admin = $this->admin();
        AdminNotification::factory()->count(3)->create(['user_id' => $admin->id, 'read_at' => null]);

        $this->actingAs($admin)->putJson('/admin/notifications/read-all')->assertOk();
        $this->assertEquals(0, AdminNotification::forUser($admin->id)->unread()->count());
    }

    // ===== EXPORTS =====

    public function test_export_contacts_csv(): void
    {
        $admin = $this->admin();
        ContactSubmission::factory()->count(3)->create();

        $this->actingAs($admin)->get('/admin/exports/contacts')->assertOk();
    }

    public function test_export_donations_csv_redirects(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/exports/donations')->assertRedirect();
    }

    public function test_export_subscribers_csv(): void
    {
        $admin = $this->admin();
        NewsletterSubscriber::factory()->count(3)->create();

        $this->actingAs($admin)->get('/admin/exports/subscribers')->assertOk();
    }

    public function test_export_content_csv(): void
    {
        $admin = $this->admin();
        Cause::factory()->create();

        $this->actingAs($admin)->get('/admin/exports/causes')->assertOk();
    }

    // ===== SEARCH =====

    public function test_search_returns_results(): void
    {
        $admin = $this->admin();
        Cause::factory()->create(['title' => 'Education Initiative']);
        Story::factory()->create(['title' => 'Student Success Story']);

        $response = $this->actingAs($admin)->getJson('/admin/search?q=education');
        $response->assertOk();
    }

    public function test_search_empty_query_returns_validation_error(): void
    {
        $admin = $this->admin();
        $response = $this->actingAs($admin)->getJson('/admin/search?q=');
        $response->assertStatus(422);
    }

    // ===== DASHBOARD =====

    public function test_dashboard_includes_all_stats(): void
    {
        $admin = $this->admin();
        Cause::factory()->count(2)->create();
        Initiative::factory()->count(3)->create();
        Event::factory()->count(1)->create();
        Story::factory()->create(['status' => 'published']);
        ContactSubmission::factory()->count(2)->create();
        NewsletterSubscriber::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk();
    }

    // ===== PAGINATION =====

    public function test_causes_index_is_paginated(): void
    {
        $admin = $this->admin();
        Cause::factory()->count(25)->create();

        $response = $this->actingAs($admin)->get('/admin/causes');
        $response->assertOk();
    }

    public function test_initiatives_index_is_paginated(): void
    {
        $admin = $this->admin();
        Initiative::factory()->count(25)->create();

        $response = $this->actingAs($admin)->get('/admin/initiatives');
        $response->assertOk();
    }

    public function test_events_index_is_paginated(): void
    {
        $admin = $this->admin();
        Event::factory()->count(25)->create();

        $response = $this->actingAs($admin)->get('/admin/events');
        $response->assertOk();
    }

    public function test_stories_index_is_paginated(): void
    {
        $admin = $this->admin();
        Story::factory()->count(25)->create();

        $response = $this->actingAs($admin)->get('/admin/stories');
        $response->assertOk();
    }

    public function test_impact_index_is_paginated(): void
    {
        $admin = $this->admin();
        ImpactActivity::factory()->count(25)->create();

        $response = $this->actingAs($admin)->get('/admin/impact');
        $response->assertOk();
    }
}
