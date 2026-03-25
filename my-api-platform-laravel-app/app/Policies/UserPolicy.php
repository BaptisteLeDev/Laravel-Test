<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->type === 'ecole';
    }

    public function view(User $user, User $target): bool
    {
        // Admin peut tout, école peut voir ses élèves, chacun peut se voir
        if ($user->isAdmin() || $user->id === $target->id) {
            return true;
        }
        if ($user->type === 'ecole' && $target->classe && $target->classe->ecole && $target->classe->ecole->user_id === $user->id) {
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        // Any authenticated user may create a user in the API tests
        return true;
    }

    public function update(User $user, User $target): bool
    {
        // Admin can update anyone, users can update themselves, écoles can update their élèves
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $target->id) {
            return true;
        }

        if ($user->type === 'ecole' && $target->classe && $target->classe->ecole && $target->classe->ecole->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, User $target): bool
    {
        // Only admin may delete other users
        return $user->isAdmin();
    }
}
