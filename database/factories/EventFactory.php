<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(2, true),
            'image' => '',
            'event_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'location' => fake()->city(),
            'initiative_id' => null,
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }

    public function upcoming(): static
    {
        return $this->published()->state(fn () => ['event_date' => fake()->dateTimeBetween('+1 week', '+3 months')]);
    }
}
