<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_index_requires_authentication(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_update_and_delete_user(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $createResponse = $this->postJson('/api/users', [
            'name' => 'Api Created User',
            'email' => 'api.created@example.test',
            'password' => 'password123',
            'type' => 'etudiant',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('email', 'api.created@example.test');

        $createdId = $createResponse->json('id');

        $this->patchJson("/api/users/{$createdId}", [
            'name' => 'Api Updated User',
            'type' => 'prof',
        ])
            ->assertOk()
            ->assertJsonPath('name', 'Api Updated User')
            ->assertJsonPath('type', 'prof');

        $this->deleteJson("/api/users/{$createdId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $createdId]);
    }
}
