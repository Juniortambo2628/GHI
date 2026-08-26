<?php

namespace Tests\Feature;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    // --- Auth ---

    public function test_unauthenticated_redirects(): void
    {
        $this->get('/admin/security')->assertRedirect();
    }

    public function test_non_admin_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin/security')->assertForbidden();
    }

    // --- Security Page ---

    public function test_admin_can_view_security_page(): void
    {
        $this->actingAs($this->admin())->get('/admin/security')->assertOk();
    }

    public function test_security_page_shows_passkeys(): void
    {
        $admin = $this->admin();
        Passkey::factory()->count(2)->create(['user_id' => $admin->id]);

        $response = $this->actingAs($admin)->get('/admin/security');
        $response->assertOk();
    }

    // --- Passkey Options ---

    public function test_passkey_options_returns_json(): void
    {
        $admin = $this->admin();
        $response = $this->actingAs($admin)->getJson('/admin/security/passkeys/options');
        $response->assertOk();
        $response->assertJsonStructure([
            'challenge',
            'rp' => ['name', 'id'],
            'user' => ['id', 'name', 'displayName'],
            'pubKeyCredParams',
            'authenticatorSelection',
            'timeout',
            'attestation',
            'excludeCredentials',
        ]);
    }

    public function test_passkey_options_stores_challenge_in_session(): void
    {
        $admin = $this->admin();
        $response = $this->actingAs($admin)->getJson('/admin/security/passkeys/options');
        $response->assertOk();

        $this->assertNotEmpty(session('passkey_challenge'));
    }

    public function test_passkey_options_excludes_existing_credentials(): void
    {
        $admin = $this->admin();
        Passkey::factory()->create(['user_id' => $admin->id, 'credential_id' => 'existing-cred-id']);

        $response = $this->actingAs($admin)->getJson('/admin/security/passkeys/options');
        $response->assertOk();
        $excludeCredentials = $response->json('excludeCredentials');
        $this->assertTrue(
            collect($excludeCredentials)->contains('id', 'existing-cred-id'),
            'Expected credential_id not found in excludeCredentials'
        );
    }

    // --- Passkey Registration ---

    public function test_passkey_register_requires_name(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->postJson('/admin/security/passkeys', [
            'credential' => ['id' => 'test', 'rawId' => 'dGVzdA==', 'type' => 'public-key', 'response' => ['clientDataJSON' => 'abc', 'attestationObject' => 'def']],
        ])->assertJsonValidationErrors('name');
    }

    public function test_passkey_register_requires_credential(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->postJson('/admin/security/passkeys', [
            'name' => 'My Key',
        ])->assertJsonValidationErrors('credential');
    }

    public function test_passkey_register_stores_passkey(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->session(['passkey_challenge' => base64_encode(random_bytes(32))]);

        $this->postJson('/admin/security/passkeys', [
            'name' => 'MacBook Pro',
            'credential' => [
                'id' => 'test-cred-id-123',
                'rawId' => base64_encode('test-cred-id-123'),
                'type' => 'public-key',
                'response' => [
                    'clientDataJSON' => base64_encode('test'),
                    'attestationObject' => base64_encode('test'),
                ],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('passkeys', [
            'user_id' => $admin->id,
            'name' => 'MacBook Pro',
            'credential_id' => 'test-cred-id-123',
        ]);
    }

    public function test_passkey_register_rejects_duplicate_credential(): void
    {
        $admin = $this->admin();
        Passkey::factory()->create(['credential_id' => 'dup-cred-id']);
        $this->actingAs($admin)->session(['passkey_challenge' => base64_encode(random_bytes(32))]);

        $this->postJson('/admin/security/passkeys', [
            'name' => 'Duplicate Key',
            'credential' => [
                'id' => 'dup-cred-id',
                'rawId' => base64_encode('dup-cred-id'),
                'type' => 'public-key',
                'response' => [
                    'clientDataJSON' => base64_encode('test'),
                    'attestationObject' => base64_encode('test'),
                ],
            ],
        ])->assertStatus(422);
    }

    public function test_passkey_register_requires_challenge_in_session(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->postJson('/admin/security/passkeys', [
            'name' => 'No Challenge',
            'credential' => [
                'id' => 'test-id',
                'rawId' => base64_encode('test-id'),
                'type' => 'public-key',
                'response' => [
                    'clientDataJSON' => base64_encode('test'),
                    'attestationObject' => base64_encode('test'),
                ],
            ],
        ])->assertStatus(422);
    }

    // --- Passkey Delete ---

    public function test_admin_can_delete_own_passkey(): void
    {
        $admin = $this->admin();
        $passkey = Passkey::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)->deleteJson("/admin/security/passkeys/{$passkey->id}")
            ->assertOk();

        $this->assertDatabaseMissing('passkeys', ['id' => $passkey->id]);
    }

    public function test_cannot_delete_other_users_passkey(): void
    {
        $admin = $this->admin();
        $other = User::factory()->create(['is_admin' => true]);
        $passkey = Passkey::factory()->create(['user_id' => $other->id]);

        $this->actingAs($admin)->deleteJson("/admin/security/passkeys/{$passkey->id}")
            ->assertNotFound();
    }

    public function test_delete_nonexistent_passkey_returns_404(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->deleteJson('/admin/security/passkeys/99999')
            ->assertNotFound();
    }

    // --- Password Change ---

    public function test_change_password_requires_current_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/security/password', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertJsonValidationErrors('current_password');
    }

    public function test_change_password_requires_new_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/security/password', [
            'current_password' => 'password',
        ])->assertJsonValidationErrors('password');
    }

    public function test_change_password_requires_confirmation(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/security/password', [
            'current_password' => 'password',
            'password' => 'newpassword123',
        ])->assertJsonValidationErrors('password');
    }

    public function test_change_password_rejects_wrong_current_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/security/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertJsonValidationErrors('current_password');
    }

    public function test_change_password_succeeds_with_correct_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/security/password', [
            'current_password' => 'password',
            'password' => 'newsecurepass123',
            'password_confirmation' => 'newsecurepass123',
        ])->assertOk();

        $admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newsecurepass123', $admin->password));
    }

    public function test_change_password_rejects_short_password(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->putJson('/admin/security/password', [
            'current_password' => 'password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertJsonValidationErrors('password');
    }
}
