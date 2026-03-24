<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Student One',
            'email' => 'student.one@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email', 'type']]);

        $this->assertDatabaseHas('users', [
            'email' => 'student.one@example.test',
            'type' => 'etudiant',
        ]);
    }

    public function test_login_returns_token_for_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login.user@example.test',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login.user@example.test',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'email']]);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_me(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email);
    }

    public function test_admin_named_user_can_login_and_change_a_user_role(): void
    {
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password123'),
            'type' => 'prof',
        ]);

        $target = User::factory()->create(['type' => 'etudiant']);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertOk();

        $token = $loginResponse->json('token');

        $this->patchJson(
            "/api/users/{$target->id}/role",
            ['role' => 'prof'],
            ['Authorization' => "Bearer {$token}"]
        )
            ->assertOk()
            ->assertJsonPath('user.type', 'prof');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'type' => 'prof',
        ]);
    }
}
