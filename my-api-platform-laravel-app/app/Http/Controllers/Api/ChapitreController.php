<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chapitre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChapitreController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        return Chapitre::with('articles')->get();
    }

    public function show(Chapitre $chapitre)
    {
        return $chapitre->load('articles');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Chapitre::class);
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'ordre' => 'nullable|integer',
            'formation_id' => 'required|exists:formations,id',
        ]);

        $chapitre = Chapitre::create($data);

        return response($chapitre, Response::HTTP_CREATED);
    }

    public function update(Request $request, Chapitre $chapitre)
    {
        $this->authorize('update', $chapitre);
        $data = $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'ordre' => 'nullable|integer',
            'formation_id' => 'sometimes|required|exists:formations,id',
        ]);

        $chapitre->update($data);

        return $chapitre;
    }

    public function destroy(Chapitre $chapitre)
    {
        $this->authorize('delete', $chapitre);
        $chapitre->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
