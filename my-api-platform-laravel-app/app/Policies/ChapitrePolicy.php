<?php

namespace App\Policies;

use App\Models\Chapitre;
use App\Models\User;

class ChapitrePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Chapitre $chapitre): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Seuls les admins ou profs peuvent créer un chapitre
        return $user->isAdmin() || $user->type === 'prof';
    }

    public function update(User $user, Chapitre $chapitre): bool
    {
        // Admin ou prof propriétaire du chapitre (via formation)
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->type === 'prof' && $chapitre->formation && $chapitre->formation->user_id === $user->id) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Chapitre $chapitre): bool
    {
        // Admin ou prof propriétaire
        return $user->isAdmin() || ($user->type === 'prof' && $chapitre->formation && $chapitre->formation->user_id === $user->id);
    }
}
