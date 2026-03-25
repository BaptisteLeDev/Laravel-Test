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

    public function test_formation_creation_access()
    {
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'admin']);
        $prof = User::factory()->create(['type' => 'prof']);
        $ecole = User::factory()->create(['type' => 'ecole']);
        $etudiant = User::factory()->create(['type' => 'etudiant']);
        $payload = ['titre' => 'Test Formation', 'user_id' => $prof->id];

        Sanctum::actingAs($admin);
        $this->postJson('/api/formations', $payload)->assertCreated();
        Sanctum::actingAs($prof);
        $this->postJson('/api/formations', $payload)->assertCreated();
        Sanctum::actingAs($ecole);
        $this->postJson('/api/formations', $payload)->assertForbidden();
        Sanctum::actingAs($etudiant);
        $this->postJson('/api/formations', $payload)->assertForbidden();
        $this->postJson('/api/formations', $payload)->assertUnauthorized();
    }

    public function test_classe_creation_access()
    {
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'admin']);
        $ecole = User::factory()->create(['type' => 'ecole']);
        $prof = User::factory()->create(['type' => 'prof']);
        $etudiant = User::factory()->create(['type' => 'etudiant']);
        $ecoleModel = Ecole::factory()->create(['user_id' => $ecole->id]);
        $payload = ['nom' => 'Classe Test'];

        Sanctum::actingAs($admin);
        $this->postJson("/api/ecoles/{$ecoleModel->id}/classes", $payload)->assertCreated();
        Sanctum::actingAs($ecole);
        $this->postJson("/api/ecoles/{$ecoleModel->id}/classes", $payload)->assertCreated();
        Sanctum::actingAs($prof);
        $this->postJson("/api/ecoles/{$ecoleModel->id}/classes", $payload)->assertForbidden();
        Sanctum::actingAs($etudiant);
        $this->postJson("/api/ecoles/{$ecoleModel->id}/classes", $payload)->assertForbidden();
        $this->postJson("/api/ecoles/{$ecoleModel->id}/classes", $payload)->assertUnauthorized();
    }

    public function test_chapitre_creation_access()
    {
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'admin']);
        $prof = User::factory()->create(['type' => 'prof']);
        $ecole = User::factory()->create(['type' => 'ecole']);
        $etudiant = User::factory()->create(['type' => 'etudiant']);
        $formation = Formation::factory()->create(['user_id' => $prof->id]);
        $payload = ['titre' => 'Chapitre Test', 'formation_id' => $formation->id];

        Sanctum::actingAs($admin);
        $this->postJson("/api/formations/{$formation->id}/chapitres", $payload)->assertCreated();
        Sanctum::actingAs($prof);
        $this->postJson("/api/formations/{$formation->id}/chapitres", $payload)->assertCreated();
        Sanctum::actingAs($ecole);
        $this->postJson("/api/formations/{$formation->id}/chapitres", $payload)->assertForbidden();
        Sanctum::actingAs($etudiant);
        $this->postJson("/api/formations/{$formation->id}/chapitres", $payload)->assertForbidden();
        $this->postJson("/api/formations/{$formation->id}/chapitres", $payload)->assertUnauthorized();
    }

    public function test_article_creation_access()
    {
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'admin']);
        $prof = User::factory()->create(['type' => 'prof']);
        $ecole = User::factory()->create(['type' => 'ecole']);
        $etudiant = User::factory()->create(['type' => 'etudiant']);
        $formation = Formation::factory()->create(['user_id' => $prof->id]);
        $chapitre = Chapitre::factory()->create(['formation_id' => $formation->id]);
        $payload = ['titre' => 'Article Test', 'chapitre_id' => $chapitre->id];

        Sanctum::actingAs($admin);
        $this->postJson("/api/chapitres/{$chapitre->id}/articles", $payload)->assertCreated();
        Sanctum::actingAs($prof);
        $this->postJson("/api/chapitres/{$chapitre->id}/articles", $payload)->assertCreated();
        Sanctum::actingAs($ecole);
        $this->postJson("/api/chapitres/{$chapitre->id}/articles", $payload)->assertForbidden();
        Sanctum::actingAs($etudiant);
        $this->postJson("/api/chapitres/{$chapitre->id}/articles", $payload)->assertForbidden();
        $this->postJson("/api/chapitres/{$chapitre->id}/articles", $payload)->assertUnauthorized();
    }

    public function test_user_update_access()
    {
        $admin = User::factory()->create(['type' => 'admin', 'name' => 'admin']);
        $ecole = User::factory()->create(['type' => 'ecole']);
        $classe = Classe::factory()->create();
        $eleve = User::factory()->create(['type' => 'etudiant', 'classe_id' => $classe->id]);
        $classe->ecole()->associate(Ecole::factory()->create(['user_id' => $ecole->id]));
        $classe->save();
        $payload = ['name' => 'Nouvel Élève'];

        Sanctum::actingAs($admin);
        $this->patchJson("/api/users/{$eleve->id}", $payload)->assertOk();
        Sanctum::actingAs($ecole);
        $this->patchJson("/api/users/{$eleve->id}", $payload)->assertOk();
        Sanctum::actingAs(User::factory()->create(['type' => 'prof']));
        $this->patchJson("/api/users/{$eleve->id}", $payload)->assertForbidden();
        Sanctum::actingAs(User::factory()->create(['type' => 'etudiant']));
        $this->patchJson("/api/users/{$eleve->id}", $payload)->assertForbidden();
        $this->patchJson("/api/users/{$eleve->id}", $payload)->assertUnauthorized();
    }
}
