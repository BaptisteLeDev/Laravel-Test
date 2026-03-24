<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_gets_own_chats_in_index(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        Chat::factory()->create(['user_id' => $owner->id, 'message' => 'owner message']);
        Chat::factory()->create(['user_id' => $other->id, 'message' => 'other message']);

        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/chats')->assertOk();

        $messages = collect($response->json('data'))->pluck('message')->all();

        $this->assertContains('owner message', $messages);
        $this->assertNotContains('other message', $messages);
    }

    public function test_user_cannot_access_chat_of_another_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $chat = Chat::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($other);

        $this->getJson("/api/chats/{$chat->id}")->assertForbidden();
    }
}
