<?php

namespace App\Policies;

use App\Models\Classe;
use App\Models\User;

class ClassePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Classe $classe): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Seuls les admins ou les utilisateurs "ecole" peuvent créer une classe
        return $user->isAdmin() || $user->type === 'ecole';
    }

    public function update(User $user, Classe $classe): bool
    {
        // Admin peut tout, sinon l'école propriétaire
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->type === 'ecole' && $classe->ecole && $classe->ecole->user_id === $user->id) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Classe $classe): bool
    {
        // Admin ou école propriétaire
        return $user->isAdmin() || ($user->type === 'ecole' && $classe->ecole && $classe->ecole->user_id === $user->id);
    }
}
