<?php

namespace Database\Factories;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminNotificationFactory extends Factory
{
    protected $model = AdminNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['contact', 'donation', 'subscriber', 'system']),
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'data' => null,
            'url' => null,
            'read_at' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => ['read_at' => now()]);
    }
}
