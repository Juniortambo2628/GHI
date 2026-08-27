<?php

namespace Database\Factories;

use App\Models\PageView;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageViewFactory extends Factory
{
    protected $model = PageView::class;

    public function definition(): array
    {
        return [
            'path' => '/' . fake()->slug(),
            'route_name' => fake()->randomElement(['home', 'causes.index', 'initiatives.index', 'events.index', 'stories.index']),
            'referrer' => fake()->optional()->url(),
            'visitor_hash' => fake()->sha256(),
            'occurred_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
