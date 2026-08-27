<?php

namespace Database\Factories;

use App\Models\GetInvolvedSubmission;
use App\Models\Initiative;
use Illuminate\Database\Eloquent\Factories\Factory;

class GetInvolvedSubmissionFactory extends Factory
{
    protected $model = GetInvolvedSubmission::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'initiative_id' => Initiative::factory(),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(['new', 'reviewed', 'contacted', 'closed']),
        ];
    }

    public function new(): static
    {
        return $this->state(fn () => ['status' => 'new']);
    }
}
