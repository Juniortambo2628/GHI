<?php

namespace Database\Factories;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasskeyFactory extends Factory
{
    protected $model = Passkey::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['MacBook Pro', 'iPhone 15', 'YubiKey 5', 'Windows Hello', 'Android Phone']),
            'credential_id' => bin2hex(random_bytes(32)),
            'credential' => [
                'type' => 'public-key',
                'publicKey' => base64_encode(random_bytes(32)),
                'signCount' => fake()->numberBetween(0, 100),
            ],
            'last_used_at' => null,
        ];
    }

    public function used(): static
    {
        return $this->state(fn () => ['last_used_at' => now()->subDays(2)]);
    }
}
