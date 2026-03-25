<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormationController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        return Formation::with('chapitres')->get();
    }

    public function show(Formation $formation)
    {
        return $formation->load('chapitres');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Formation::class);
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        $formation = Formation::create($data);

        return response($formation, Response::HTTP_CREATED);
    }

    public function update(Request $request, Formation $formation)
    {
        $this->authorize('update', $formation);
        $data = $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        $formation->update($data);

        return $formation;
    }

    public function destroy(Formation $formation)
    {
        $this->authorize('delete', $formation);
        $formation->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
