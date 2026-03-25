<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(3),
            'contenu' => fake()->paragraph(),
            'ordre' => fake()->numberBetween(1, 10),
            'chapitre_id' => \App\Models\Chapitre::factory(),
        ];
    }
}
