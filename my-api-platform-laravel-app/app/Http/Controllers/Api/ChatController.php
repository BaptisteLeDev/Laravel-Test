<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $chats = Chat::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($chats);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
            'response' => ['nullable', 'string'],
        ]);

        $chat = Chat::query()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'response' => $validated['response'] ?? null,
        ]);

        return response()->json($chat, 201);
    }

    public function show(Request $request, Chat $chat): JsonResponse
    {
        if ($chat->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($chat);
    }

    public function update(Request $request, Chat $chat): JsonResponse
    {
        if ($chat->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => ['sometimes', 'string'],
            'response' => ['sometimes', 'nullable', 'string'],
        ]);

        $chat->update($validated);

        return response()->json($chat->fresh());
    }

    public function destroy(Request $request, Chat $chat): JsonResponse
    {
        if ($chat->user_id !== $request->user()->id) {
            abort(403);
        }

        $chat->delete();

        return response()->json([], 204);
    }
}