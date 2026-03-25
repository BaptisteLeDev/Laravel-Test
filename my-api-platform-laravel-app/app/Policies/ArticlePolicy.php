<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Article $article): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Seuls les admins ou profs peuvent créer un article
        return $user->isAdmin() || $user->type === 'prof';
    }

    public function update(User $user, Article $article): bool
    {
        // Admin ou prof propriétaire du chapitre parent
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->type === 'prof' && $article->chapitre && $article->chapitre->formation && $article->chapitre->formation->user_id === $user->id) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Article $article): bool
    {
        // Admin ou prof propriétaire
        return $user->isAdmin() || ($user->type === 'prof' && $article->chapitre && $article->chapitre->formation && $article->chapitre->formation->user_id === $user->id);
    }
}
