<?php

namespace Tests\Feature;

use App\Models\Cause;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCauseCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Education For All',
            'description' => 'A cause about education.',
            'status' => 'draft',
            'icon' => 'book',
            'image' => '',
            'display_order' => 0,
            'quote' => '',
        ], $overrides);
    }

    // --- Authentication ---

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/admin/causes')->assertRedirect();
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/causes')->assertForbidden();
    }

    // --- Index ---

    public function test_admin_can_list_causes(): void
    {
        Cause::factory()->count(3)->create();
        $this->actingAs($this->admin())->get('/admin/causes')->assertOk();
    }

    public function test_index_filters_by_status(): void
    {
        Cause::factory()->create(['status' => 'published']);
        Cause::factory()->create(['status' => 'draft']);
        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/causes?status=published')->assertOk();
    }

    // --- Create ---

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/causes/create')->assertOk();
    }

    public function test_admin_can_create_a_cause(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/causes', $this->validPayload())
            ->assertRedirect();
        $this->assertDatabaseHas('causes', ['title' => 'Education For All']);
    }

    public function test_create_generates_slug(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/causes', $this->validPayload());
        $this->assertDatabaseHas('causes', ['slug' => 'education-for-all']);
    }

    public function test_create_validates_title_is_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/causes', $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_create_validates_status_is_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/causes', $this->validPayload(['status' => '']))
            ->assertSessionHasErrors('status');
    }

    public function test_create_validates_status_value(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/causes', $this->validPayload(['status' => 'invalid']))
            ->assertSessionHasErrors('status');
    }

    // --- Show ---

    public function test_admin_can_view_cause_detail(): void
    {
        $cause = Cause::factory()->create();
        $this->actingAs($this->admin())->get("/admin/causes/{$cause->id}")->assertOk();
    }

    // --- Edit ---

    public function test_admin_can_view_edit_form(): void
    {
        $cause = Cause::factory()->create();
        $this->actingAs($this->admin())->get("/admin/causes/{$cause->id}/edit")->assertOk();
    }

    // --- Update ---

    public function test_admin_can_update_a_cause(): void
    {
        $cause = Cause::factory()->create(['title' => 'Old Title']);
        $this->actingAs($this->admin())
            ->put("/admin/causes/{$cause->id}", $this->validPayload(['title' => 'New Title']))
            ->assertRedirect();
        $this->assertDatabaseHas('causes', ['id' => $cause->id, 'title' => 'New Title']);
    }

    public function test_update_validates_title_is_required(): void
    {
        $cause = Cause::factory()->create();
        $this->actingAs($this->admin())
            ->put("/admin/causes/{$cause->id}", $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    // --- Delete ---

    public function test_admin_can_delete_a_cause(): void
    {
        $cause = Cause::factory()->create();
        $this->actingAs($this->admin())
            ->delete("/admin/causes/{$cause->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('causes', ['id' => $cause->id]);
    }

    // --- Non-admin cannot mutate ---

    public function test_non_admin_cannot_create_cause(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/causes', $this->validPayload())
            ->assertForbidden();
    }

    public function test_non_admin_cannot_update_cause(): void
    {
        $cause = Cause::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->put("/admin/causes/{$cause->id}", $this->validPayload())
            ->assertForbidden();
    }

    public function test_non_admin_cannot_delete_cause(): void
    {
        $cause = Cause::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->delete("/admin/causes/{$cause->id}")
            ->assertForbidden();
    }
}
