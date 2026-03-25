<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiControllersTest extends TestCase
{
    use RefreshDatabase;

    public function test_formations_index_returns_ok()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/formations')
            ->assertStatus(200);
    }

    public function test_progressions_index_returns_ok()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/progressions')
            ->assertStatus(200);
    }
}
