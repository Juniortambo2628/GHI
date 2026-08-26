<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\ImpactActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminImpactCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Well Construction',
            'description' => 'Built a well for the community.',
            'status' => 'draft',
            'event_id' => null,
            'people_affected' => 200,
            'outcome_summary' => 'Clean water access',
            'image' => '',
            'display_order' => 0,
            'metric_type' => 'people',
            'metric_value' => 200,
            'activity_date' => now()->format('Y-m-d'),
            'location' => 'Mombasa',
            'featured' => false,
        ], $overrides);
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/impact')->assertForbidden();
    }

    public function test_admin_can_list_impact(): void
    {
        ImpactActivity::factory()->count(3)->create();
        $this->actingAs($this->admin())->get('/admin/impact')->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/impact/create')->assertOk();
    }

    public function test_admin_can_create_impact(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/impact', $this->validPayload())
            ->assertRedirect();
        $this->assertDatabaseHas('impact_activities', ['title' => 'Well Construction']);
    }

    public function test_create_generates_slug(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/impact', $this->validPayload());
        $this->assertDatabaseHas('impact_activities', ['slug' => 'well-construction']);
    }

    public function test_create_validates_title_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/impact', $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_create_validates_event_id_exists(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/impact', $this->validPayload(['event_id' => 999]))
            ->assertSessionHasErrors('event_id');
    }

    public function test_create_validates_people_affected_is_integer(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/impact', $this->validPayload(['people_affected' => 'abc']))
            ->assertSessionHasErrors('people_affected');
    }

    public function test_admin_can_view_impact_detail(): void
    {
        $impact = ImpactActivity::factory()->create();
        $this->actingAs($this->admin())->get("/admin/impact/{$impact->id}")->assertOk();
    }

    public function test_admin_can_view_edit_form(): void
    {
        $impact = ImpactActivity::factory()->create();
        $this->actingAs($this->admin())->get("/admin/impact/{$impact->id}/edit")->assertOk();
    }

    public function test_admin_can_update_impact(): void
    {
        $impact = ImpactActivity::factory()->create(['title' => 'Old']);
        $this->actingAs($this->admin())
            ->put("/admin/impact/{$impact->id}", $this->validPayload(['title' => 'New']))
            ->assertRedirect();
        $this->assertDatabaseHas('impact_activities', ['id' => $impact->id, 'title' => 'New']);
    }

    public function test_update_validates_title_required(): void
    {
        $impact = ImpactActivity::factory()->create();
        $this->actingAs($this->admin())
            ->put("/admin/impact/{$impact->id}", $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_delete_impact(): void
    {
        $impact = ImpactActivity::factory()->create();
        $this->actingAs($this->admin())
            ->delete("/admin/impact/{$impact->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('impact_activities', ['id' => $impact->id]);
    }

    public function test_admin_can_filter_by_date_range(): void
    {
        ImpactActivity::factory()->create(['activity_date' => now()->subWeek()]);
        ImpactActivity::factory()->create(['activity_date' => now()->subMonths(2)]);
        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/impact?from=' . now()->subMonth()->format('Y-m-d'))->assertOk();
        $this->actingAs($admin)->get('/admin/impact?to=' . now()->subDays(3)->format('Y-m-d'))->assertOk();
    }

    public function test_non_admin_cannot_create(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/impact', $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_update(): void
    {
        $impact = ImpactActivity::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->put("/admin/impact/{$impact->id}", $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_delete(): void
    {
        $impact = ImpactActivity::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->delete("/admin/impact/{$impact->id}")->assertForbidden();
    }
}
