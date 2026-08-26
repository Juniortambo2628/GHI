<?php

namespace Database\Factories;

use App\Models\ContactSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactSubmissionFactory extends Factory
{
    protected $model = ContactSubmission::class;

    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(['new', 'read', 'archived']),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
