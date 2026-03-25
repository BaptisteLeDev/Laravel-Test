<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Progression;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgressionController extends Controller
{
    public function index()
    {
        return Progression::with(['user', 'article'])->get();
    }

    public function show(Progression $progression)
    {
        return $progression->load(['user', 'article']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'article_id' => 'required|exists:articles,id',
            'termine' => 'sometimes|boolean',
            'termine_at' => 'nullable|date',
        ]);

        $progression = Progression::create($data);

        return response($progression, Response::HTTP_CREATED);
    }

    public function update(Request $request, Progression $progression)
    {
        $data = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'article_id' => 'sometimes|required|exists:articles,id',
            'termine' => 'sometimes|boolean',
            'termine_at' => 'nullable|date',
        ]);

        $progression->update($data);

        return $progression;
    }

    public function destroy(Progression $progression)
    {
        $progression->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
