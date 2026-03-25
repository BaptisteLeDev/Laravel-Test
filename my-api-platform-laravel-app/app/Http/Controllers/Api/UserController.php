<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
    }
    public function index(): JsonResponse
    {
        return response()->json(User::query()->latest()->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'type' => ['nullable', 'in:prof,ecole,etudiant'],
            'classe_id' => ['nullable', 'exists:classes,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'] ?? 'etudiant',
            'classe_id' => $validated['classe_id'] ?? null,
        ]);

        return response()->json($user, 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['sometimes', 'string', 'min:8'],
            'type' => ['sometimes', 'in:prof,ecole,etudiant'],
            'classe_id' => ['sometimes', 'nullable', 'exists:classes,id'],
        ]);

        if (array_key_exists('password', $validated)) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->fresh());
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->json([], 204);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);
        $validated = $request->validate([
            'role' => ['required', 'in:prof,ecole,etudiant'],
        ]);

        $user->update(['type' => $validated['role']]);

        return response()->json([
            'message' => 'User role updated successfully.',
            'user' => $user->fresh(),
        ]);
    }
}
