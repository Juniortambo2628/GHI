<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Passkey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserSecurityController extends Controller
{
    public function index(Request $request)
    {
        $passkeys = Passkey::where('user_id', $request->user()->id)
            ->orderByDesc('last_used_at')
            ->get()
            ->map(fn ($pk) => [
                'id' => $pk->id,
                'name' => $pk->name,
                'credential_id' => $pk->credential_id,
                'last_used_at' => $pk->last_used_at?->diffForHumans(),
                'created_at' => $pk->created_at->diffForHumans(),
            ]);

        $user = $request->user();

        return inertia('Admin/Security/Index', [
            'passkeys' => $passkeys,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified' => ! is_null($user->email_verified_at),
                'two_factor_enabled' => false,
            ],
            'rp' => [
                'id' => config('app.url') ? parse_url(config('app.url'), PHP_URL_HOST) : request()->getHost(),
                'name' => config('app.name', 'GHI'),
            ],
        ]);
    }

    public function passkeyOptions(Request $request): JsonResponse
    {
        $user = $request->user();

        $challenge = random_bytes(32);

        $request->session()->put('passkey_challenge', base64_encode($challenge));
        $request->session()->put('passkey_user_id', $user->id);

        $excludeCredentials = Passkey::where('user_id', $user->id)
            ->pluck('credential_id')
            ->values()
            ->all();

        return response()->json([
            'challenge' => base64_encode($challenge),
            'rp' => [
                'name' => config('app.name', 'GHI'),
                'id' => config('app.url') ? parse_url(config('app.url'), PHP_URL_HOST) : request()->getHost(),
            ],
            'user' => [
                'id' => base64_encode((string) $user->id),
                'name' => $user->email,
                'displayName' => $user->name,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7],
                ['type' => 'public-key', 'alg' => -257],
            ],
            'authenticatorSelection' => [
                'residentKey' => 'preferred',
                'userVerification' => 'preferred',
            ],
            'timeout' => 60000,
            'attestation' => 'none',
            'excludeCredentials' => array_map(fn ($id) => ['type' => 'public-key', 'id' => $id], $excludeCredentials),
        ]);
    }

    public function passkeyRegister(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'credential' => 'required|array',
            'credential.id' => 'required|string',
            'credential.rawId' => 'required|string',
            'credential.response' => 'required|array',
            'credential.response.clientDataJSON' => 'required|string',
            'credential.response.attestationObject' => 'required|string',
            'credential.type' => 'required|string|in:public-key',
        ]);

        $challenge = $request->session()->get('passkey_challenge');
        if (! $challenge) {
            return response()->json(['message' => 'Registration session expired.'], 422);
        }

        $request->session()->forget('passkey_challenge');

        $credentialId = $validated['credential']['id'];
        if (Passkey::where('credential_id', $credentialId)->exists()) {
            return response()->json(['message' => 'This passkey is already registered.'], 422);
        }

        $passkey = Passkey::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'credential_id' => $credentialId,
            'credential' => $validated['credential'],
        ]);

        return response()->json([
            'message' => 'Passkey registered successfully.',
            'passkey' => [
                'id' => $passkey->id,
                'name' => $passkey->name,
                'credential_id' => $passkey->credential_id,
                'created_at' => $passkey->created_at->diffForHumans(),
            ],
        ]);
    }

    public function passkeyDelete(Request $request, int $id): JsonResponse
    {
        $passkey = Passkey::where('user_id', $request->user()->id)->findOrFail($id);
        $passkey->delete();

        return response()->json(['message' => 'Passkey removed.']);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->update(['password' => $validated['password']]);

        return response()->json(['message' => 'Password updated successfully.']);
    }
}
