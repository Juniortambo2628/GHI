<?php

namespace Database\Factories;

use App\Models\ImpactActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImpactActivityFactory extends Factory
{
    protected $model = ImpactActivity::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => fake()->paragraph(),
            'event_id' => null,
            'people_affected' => fake()->numberBetween(10, 5000),
            'outcome_summary' => fake()->sentence(),
            'image' => '',
            'display_order' => 0,
            'metric_type' => fake()->randomElement(['people', 'families', 'schools', null]),
            'metric_value' => fake()->randomFloat(2, 0, 100),
            'activity_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'location' => fake()->city(),
            'featured' => fake()->boolean(20),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }
}
