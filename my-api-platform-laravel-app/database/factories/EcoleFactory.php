<?php

namespace Database\Factories;

use App\Models\Ecole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ecole>
 */
class EcoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->company(),
            'user_id' => \App\Models\User::factory()->state(['type' => 'ecole']),
        ];
    }
}
