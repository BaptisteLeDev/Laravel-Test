<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Classe;
use App\Models\Ecole;
use App\Models\Formation;
use App\Models\Chapitre;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RBACAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test access for each endpoint with: (1) authorized user, (2) unauthorized role, (3) no auth
     */
    public function test_rbac_access_matrix()
    {
        // Setup users
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'admin']);
        $ecole = User::factory()->create(['type' => 'ecole']);
        $prof = User::factory()->create(['type' => 'prof']);
        $etudiant = User::factory()->create(['type' => 'etudiant']);

        // Example: Création de formation (admin/prof OK, autres refusés)
        $payload = [
            'titre' => 'Test Formation',
            'user_id' => $prof->id,
        ];

        // 1. Admin peut créer
        Sanctum::actingAs($admin);
        $this->postJson('/api/formations', $payload)->assertCreated();

        // 2. Prof peut créer
        Sanctum::actingAs($prof);
        $this->postJson('/api/formations', $payload)->assertCreated();

        // 3. Ecole ne peut pas créer
        Sanctum::actingAs($ecole);
        $this->postJson('/api/formations', $payload)->assertForbidden();

        // 4. Non authentifié
        $this->postJson('/api/formations', $payload)->assertUnauthorized();

        // Répéter pour d'autres routes critiques (classe, chapitre, article, user update...)
    }
}
