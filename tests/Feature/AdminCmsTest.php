<?php

namespace Tests\Feature;

use App\Models\PageView;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_are_denied_from_admin(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))->get('/admin')->assertForbidden();
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $payload = ['site_name' => 'Updated GHI', 'site_description' => 'Description', 'contact_email' => 'admin@example.com', 'contact_phone' => '', 'facebook_url' => '', 'instagram_url' => '', 'footer_text' => 'Footer', 'homepage_hero' => '[]', 'homepage_sections' => '{}'];

        $this->actingAs($admin)->put('/admin/settings', $payload)->assertRedirect();
        $this->assertSame('Updated GHI', SiteSetting::where('key', 'site_name')->value('value'));
    }

    public function test_image_upload_returns_a_stored_path(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin)->withHeader('Accept', 'application/json')->post('/api/upload/image', ['file' => UploadedFile::fake()->image('hero.jpg')]);

        $response->assertOk()->assertJsonPath('success', true);
        Storage::disk('public')->assertExists($response->json('path'));
    }

    public function test_admin_can_open_every_content_and_cms_screen(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        foreach (['/admin', '/admin/causes', '/admin/initiatives', '/admin/events', '/admin/impact', '/admin/stories', '/admin/analytics', '/admin/contacts', '/admin/subscribers', '/admin/settings', '/admin/media'] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_public_visits_are_recorded_and_search_is_available(): void
    {
        $this->get('/')->assertOk();
        $this->get('/search?q=community')->assertOk();

        $this->assertGreaterThanOrEqual(1, PageView::where('path', '/')->count());
        $this->assertGreaterThanOrEqual(1, PageView::where('path', '/search')->count());
    }

    public function test_payment_records_are_read_only(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/donations')->assertRedirect();
        $this->actingAs($admin)->post('/admin/donations', [])->assertStatus(405);
    }
}
