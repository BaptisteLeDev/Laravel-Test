<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ecole;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EcoleController extends Controller
{
    public function index(): JsonResponse
    {
        $ecoles = Ecole::query()
            ->with(['user', 'classes'])
            ->latest()
            ->paginate(15);

        return response()->json($ecoles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id', 'unique:ecoles,user_id'],
        ]);

        $user = User::query()->findOrFail($validated['user_id']);
        $user->update(['type' => 'ecole']);

        $ecole = Ecole::query()->create($validated)->load(['user', 'classes']);

        return response()->json($ecole, 201);
    }

    public function show(Ecole $ecole): JsonResponse
    {
        return response()->json($ecole->load(['user', 'classes']));
    }

    public function update(Request $request, Ecole $ecole): JsonResponse
    {
        $validated = $request->validate([
            'nom' => ['sometimes', 'string', 'max:255'],
            'user_id' => [
                'sometimes',
                'exists:users,id',
                Rule::unique('ecoles', 'user_id')->ignore($ecole->id),
            ],
        ]);

        if (array_key_exists('user_id', $validated)) {
            $user = User::query()->findOrFail($validated['user_id']);
            $user->update(['type' => 'ecole']);
        }

        $ecole->update($validated);

        return response()->json($ecole->fresh()->load(['user', 'classes']));
    }

    public function destroy(Ecole $ecole): JsonResponse
    {
        $ecole->delete();

        return response()->json([], 204);
    }
}