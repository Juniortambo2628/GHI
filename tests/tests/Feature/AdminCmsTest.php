<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminCmsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_non_admin_users_are_denied_from_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_view_settings_and_update_them(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/settings')->assertOk();
        $this->actingAs($admin)->put('/admin/settings', [
            'site_name' => 'Updated GHI',
            'site_description' => 'Updated description',
            'contact_email' => 'admin@example.com',
            'contact_phone' => '',
            'facebook_url' => '',
            'instagram_url' => '',
            'footer_text' => 'Updated footer',
            'homepage_hero' => '[]',
            'homepage_sections' => '{}',
        ])->assertRedirect();

        $this->assertSame('Updated GHI', SiteSetting::where('key', 'site_name')->value('value'));
    }

    public function test_image_upload_returns_canonical_path(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('hero.jpg', 1200, 800);

        $response = $this->withHeader('Accept', 'application/json')->post('/api/upload/image', ['file' => $file]);

        $response->assertOk()->assertJsonPath('success', true)->assertJsonStructure(['path', 'filename', 'size']);
        Storage::disk('public')->assertExists($response->json('path'));
    }
}
