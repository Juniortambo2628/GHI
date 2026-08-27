<?php

namespace Tests\Feature;

use App\Models\Initiative;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInitiativeCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Clean Water Initiative',
            'description' => 'Providing clean water.',
            'content' => '<p>Full content here.</p>',
            'status' => 'draft',
            'category' => 'health',
            'cause_ids' => [],
            'image' => '',
        ], $overrides);
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/initiatives')->assertForbidden();
    }

    public function test_admin_can_list_initiatives(): void
    {
        Initiative::factory()->count(3)->create();
        $this->actingAs($this->admin())->get('/admin/initiatives')->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/initiatives/create')->assertOk();
    }

    public function test_admin_can_create_an_initiative(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/initiatives', $this->validPayload())
            ->assertRedirect();
        $this->assertDatabaseHas('initiatives', ['title' => 'Clean Water Initiative']);
    }

    public function test_create_generates_slug(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/initiatives', $this->validPayload());
        $this->assertDatabaseHas('initiatives', ['slug' => 'clean-water-initiative']);
    }

    public function test_create_validates_title_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/initiatives', $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_create_validates_category_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/initiatives', $this->validPayload(['category' => '']))
            ->assertSessionHasErrors('category');
    }

    public function test_create_validates_status_value(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/initiatives', $this->validPayload(['status' => 'invalid']))
            ->assertSessionHasErrors('status');
    }

    public function test_create_validates_cause_ids_exist(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/initiatives', $this->validPayload(['cause_ids' => [999]]))
            ->assertSessionHasErrors('cause_ids.0');
    }

    public function test_admin_can_view_initiative_detail(): void
    {
        $initiative = Initiative::factory()->create();
        $this->actingAs($this->admin())->get("/admin/initiatives/{$initiative->id}")->assertOk();
    }

    public function test_admin_can_view_edit_form(): void
    {
        $initiative = Initiative::factory()->create();
        $this->actingAs($this->admin())->get("/admin/initiatives/{$initiative->id}/edit")->assertOk();
    }

    public function test_admin_can_update_an_initiative(): void
    {
        $initiative = Initiative::factory()->create(['title' => 'Old']);
        $this->actingAs($this->admin())
            ->put("/admin/initiatives/{$initiative->id}", $this->validPayload(['title' => 'New']))
            ->assertRedirect();
        $this->assertDatabaseHas('initiatives', ['id' => $initiative->id, 'title' => 'New']);
    }

    public function test_update_validates_title_required(): void
    {
        $initiative = Initiative::factory()->create();
        $this->actingAs($this->admin())
            ->put("/admin/initiatives/{$initiative->id}", $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_delete_an_initiative(): void
    {
        $initiative = Initiative::factory()->create();
        $this->actingAs($this->admin())
            ->delete("/admin/initiatives/{$initiative->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('initiatives', ['id' => $initiative->id]);
    }

    public function test_admin_can_filter_by_category(): void
    {
        Initiative::factory()->create(['category' => 'education']);
        Initiative::factory()->create(['category' => 'health']);
        $this->actingAs($this->admin())->get('/admin/initiatives?category=education')->assertOk();
    }

    public function test_non_admin_cannot_create(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/initiatives', $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_update(): void
    {
        $initiative = Initiative::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->put("/admin/initiatives/{$initiative->id}", $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_delete(): void
    {
        $initiative = Initiative::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->delete("/admin/initiatives/{$initiative->id}")->assertForbidden();
    }
}
