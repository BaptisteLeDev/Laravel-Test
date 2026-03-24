<?php

namespace Tests\Feature;

use App\Models\Classe;
use App\Models\Ecole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EcoleClasseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_ecole_sets_user_type_to_ecole(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $user = User::factory()->create(['type' => 'etudiant']);

        $response = $this->postJson('/api/ecoles', [
            'nom' => 'Ecole Test',
            'user_id' => $user->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('nom', 'Ecole Test');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'type' => 'ecole',
        ]);
    }

    public function test_nested_classe_show_returns_404_when_classe_belongs_to_other_ecole(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $ecoleA = Ecole::factory()->create();
        $ecoleB = Ecole::factory()->create();
        $classe = Classe::factory()->create(['ecole_id' => $ecoleB->id]);

        $this->getJson("/api/ecoles/{$ecoleA->id}/classes/{$classe->id}")
            ->assertNotFound();
    }
}
