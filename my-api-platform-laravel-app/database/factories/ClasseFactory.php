<?php

namespace Database\Factories;

use App\Models\Classe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classe>
 */
class ClasseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->randomElement(['Terminale', 'Première', 'Seconde', 'BTS 1', 'BTS 2', 'Licence 1', 'Licence 2', 'Licence 3']) . ' ' . fake()->randomLetter(),
            'ecole_id' => \App\Models\Ecole::factory(),
        ];
    }
}
