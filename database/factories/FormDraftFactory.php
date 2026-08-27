<?php

namespace Database\Factories;

use App\Models\FormDraft;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormDraftFactory extends Factory
{
    protected $model = FormDraft::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'form_key' => fake()->unique()->slug(),
            'data' => json_encode(['field' => fake()->word()]),
        ];
    }
}
