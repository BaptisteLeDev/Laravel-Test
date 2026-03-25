<?php

namespace Database\Factories;

use App\Models\Progression;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Progression>
 */
class ProgressionFactory extends Factory
{
    protected $model = Progression::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'article_id' => \App\Models\Article::factory(),
            'termine' => fake()->boolean(20),
            'termine_at' => fake()->optional()->dateTime(),
        ];
    }
}
