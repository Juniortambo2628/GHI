<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventImageFactory extends Factory
{
    protected $model = EventImage::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'path' => 'events/'.fake()->uuid().'.jpg',
            'type' => fake()->randomElement(['image', 'video']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
