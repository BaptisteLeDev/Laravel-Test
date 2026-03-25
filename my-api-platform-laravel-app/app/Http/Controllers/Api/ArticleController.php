<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index()
    {
        return Article::all();
    }

    public function show(Article $article)
    {
        return $article;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'nullable|string',
            'ordre' => 'nullable|integer',
            'chapitre_id' => 'required|exists:chapitres,id',
        ]);

        $article = Article::create($data);

        return response($article, Response::HTTP_CREATED);
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'titre' => 'sometimes|required|string|max:255',
            'contenu' => 'nullable|string',
            'ordre' => 'nullable|integer',
            'chapitre_id' => 'sometimes|required|exists:chapitres,id',
        ]);

        $article->update($data);

        return $article;
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
