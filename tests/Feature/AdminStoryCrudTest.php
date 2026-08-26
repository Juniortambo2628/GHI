<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Community Transformation',
            'content' => 'How the community was transformed.',
            'status' => 'draft',
            'author' => 'Jane Doe',
            'image' => '',
            'featured_image' => '',
            'category' => 'education',
        ], $overrides);
    }

    public function test_non_admin_is_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/stories')->assertForbidden();
    }

    public function test_admin_can_list_stories(): void
    {
        Story::factory()->count(3)->create();
        $this->actingAs($this->admin())->get('/admin/stories')->assertOk();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/stories/create')->assertOk();
    }

    public function test_admin_can_create_a_story(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/stories', $this->validPayload())
            ->assertRedirect();
        $this->assertDatabaseHas('stories', ['title' => 'Community Transformation']);
    }

    public function test_create_generates_slug(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/stories', $this->validPayload());
        $this->assertDatabaseHas('stories', ['slug' => 'community-transformation']);
    }

    public function test_create_validates_title_required(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/stories', $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_create_validates_status_value(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/stories', $this->validPayload(['status' => 'invalid']))
            ->assertSessionHasErrors('status');
    }

    public function test_admin_can_view_story_detail(): void
    {
        $story = Story::factory()->create();
        $this->actingAs($this->admin())->get("/admin/stories/{$story->id}")->assertOk();
    }

    public function test_admin_can_view_edit_form(): void
    {
        $story = Story::factory()->create();
        $this->actingAs($this->admin())->get("/admin/stories/{$story->id}/edit")->assertOk();
    }

    public function test_admin_can_update_a_story(): void
    {
        $story = Story::factory()->create(['title' => 'Old']);
        $this->actingAs($this->admin())
            ->put("/admin/stories/{$story->id}", $this->validPayload(['title' => 'New']))
            ->assertRedirect();
        $this->assertDatabaseHas('stories', ['id' => $story->id, 'title' => 'New']);
    }

    public function test_update_validates_title_required(): void
    {
        $story = Story::factory()->create();
        $this->actingAs($this->admin())
            ->put("/admin/stories/{$story->id}", $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_delete_a_story(): void
    {
        $story = Story::factory()->create();
        $this->actingAs($this->admin())
            ->delete("/admin/stories/{$story->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('stories', ['id' => $story->id]);
    }

    public function test_admin_can_filter_by_category(): void
    {
        Story::factory()->create(['category' => 'education']);
        Story::factory()->create(['category' => 'health']);
        $this->actingAs($this->admin())->get('/admin/stories?category=education')->assertOk();
    }

    public function test_admin_can_filter_by_status(): void
    {
        Story::factory()->published()->create();
        Story::factory()->create(['status' => 'draft']);
        $this->actingAs($this->admin())->get('/admin/stories?status=published')->assertOk();
    }

    public function test_non_admin_cannot_create(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->post('/admin/stories', $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_update(): void
    {
        $story = Story::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->put("/admin/stories/{$story->id}", $this->validPayload())->assertForbidden();
    }

    public function test_non_admin_cannot_delete(): void
    {
        $story = Story::factory()->create();
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->delete("/admin/stories/{$story->id}")->assertForbidden();
    }
}
