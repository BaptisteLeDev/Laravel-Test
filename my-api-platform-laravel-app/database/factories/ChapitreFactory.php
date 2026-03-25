<?php

namespace Database\Factories;

use App\Models\Chapitre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chapitre>
 */
class ChapitreFactory extends Factory
{
    protected $model = Chapitre::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(4),
            'ordre' => fake()->numberBetween(1, 10),
            'formation_id' => \App\Models\Formation::factory(),
        ];
    }
}
