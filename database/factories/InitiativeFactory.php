<?php

namespace Database\Factories;

use App\Models\Initiative;
use Illuminate\Database\Eloquent\Factories\Factory;

class InitiativeFactory extends Factory
{
    protected $model = Initiative::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'image' => '',
            'category' => fake()->randomElement(['livelihood', 'education', 'health', 'empowerment', 'partnerships']),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }
}
