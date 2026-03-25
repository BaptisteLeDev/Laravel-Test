<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Ecole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClasseController extends Controller
{
    public function __construct()
    {
    }
    public function index(Ecole $ecole): JsonResponse
    {
        $classes = $ecole->classes()->latest()->paginate(15);

        return response()->json($classes);
    }

    public function store(Request $request, Ecole $ecole): JsonResponse
    {
        $this->authorize('create', Classe::class);
        Log::info('ClasseController::store auth state', [
            'check' => auth()->check(),
            'user_id' => optional(auth()->user())->id,
            'default_guard' => app('auth')->getDefaultDriver(),
        ]);
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
        ]);

        $classe = $ecole->classes()->create($validated);

        return response()->json($classe, 201);
    }

    public function show(Ecole $ecole, Classe $classe): JsonResponse
    {
        abort_unless($classe->ecole_id === $ecole->id, 404);

        return response()->json($classe->load(['ecole', 'eleves', 'formations']));
    }

    public function update(Request $request, Ecole $ecole, Classe $classe): JsonResponse
    {
        abort_unless($classe->ecole_id === $ecole->id, 404);

        $validated = $request->validate([
            'nom' => ['sometimes', 'string', 'max:255'],
        ]);

        $classe->update($validated);

        return response()->json($classe->fresh()->load(['ecole', 'eleves', 'formations']));
    }

    public function destroy(Ecole $ecole, Classe $classe): JsonResponse
    {
        abort_unless($classe->ecole_id === $ecole->id, 404);

        $classe->delete();

        return response()->json([], 204);
    }
}
