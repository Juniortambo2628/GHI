<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Community Workshop',
            'description' => 'A workshop for the community.',
            'content' => '',
            'status' => 'draft',
            'event_date' => now()->addWeek()->format('Y-m-d'),
            'location' => 'Nairobi',
            'initiative_id' => null,
            'image' => '',
        ], $overrides);
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/events')->assertForbidden();
    }

    public function test_admin_can_list_events(): void
    {
        Event::factory()->count(3)->create();
        $this->actingAs($this->admin())->get('/admin/events')->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/events/create')->assertOk();
    }

    public function test_admin_can_create_an_event(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/events', $this->validPayload())
            ->assertRedirect();
        $this->assertDatabaseHas('events', ['title' => 'Community Workshop']);
    }

    public function test_create_generates_slug(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/events', $this->validPayload());
        $this->assertDatabaseHas('events', ['slug' => 'community-workshop']);
    }

    public function test_create_validates_title_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/events', $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_create_validates_event_date_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/events', $this->validPayload(['event_date' => '']))
            ->assertSessionHasErrors('event_date');
    }

    public function test_create_validates_event_date_is_date(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/events', $this->validPayload(['event_date' => 'not-a-date']))
            ->assertSessionHasErrors('event_date');
    }

    public function test_create_validates_initiative_id_exists(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/events', $this->validPayload(['initiative_id' => 999]))
            ->assertSessionHasErrors('initiative_id');
    }

    public function test_admin_can_view_event_detail(): void
    {
        $event = Event::factory()->create();
        $this->actingAs($this->admin())->get("/admin/events/{$event->id}")->assertOk();
    }

    public function test_admin_can_view_edit_form(): void
    {
        $event = Event::factory()->create();
        $this->actingAs($this->admin())->get("/admin/events/{$event->id}/edit")->assertOk();
    }

    public function test_admin_can_update_an_event(): void
    {
        $event = Event::factory()->create(['title' => 'Old']);
        $this->actingAs($this->admin())
            ->put("/admin/events/{$event->id}", $this->validPayload(['title' => 'New']))
            ->assertRedirect();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'New']);
    }

    public function test_update_validates_title_required(): void
    {
        $event = Event::factory()->create();
        $this->actingAs($this->admin())
            ->put("/admin/events/{$event->id}", $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_delete_an_event(): void
    {
        $event = Event::factory()->create();
        $this->actingAs($this->admin())
            ->delete("/admin/events/{$event->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_admin_can_filter_by_date_range(): void
    {
        Event::factory()->create(['event_date' => now()->addWeek()]);
        Event::factory()->create(['event_date' => now()->addMonths(2)]);
        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/events?from='.now()->addDays(3)->format('Y-m-d'))->assertOk();
        $this->actingAs($admin)->get('/admin/events?to='.now()->addMonth()->format('Y-m-d'))->assertOk();
    }

    public function test_non_admin_cannot_create(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/events', $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_update(): void
    {
        $event = Event::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->put("/admin/events/{$event->id}", $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_delete(): void
    {
        $event = Event::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->delete("/admin/events/{$event->id}")->assertForbidden();
    }
}
