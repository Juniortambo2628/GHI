<?php

namespace Database\Factories;

use App\Models\Cause;
use Illuminate\Database\Eloquent\Factories\Factory;

class CauseFactory extends Factory
{
    protected $model = Cause::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => fake()->paragraph(),
            'quote' => fake()->sentence(),
            'icon' => fake()->randomElement(['globe2', 'book', 'heart', 'people', 'lightbulb']),
            'image' => '',
            'display_order' => fake()->numberBetween(0, 20),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }
}
