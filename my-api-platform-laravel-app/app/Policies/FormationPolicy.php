<?php

namespace App\Policies;

use App\Models\Formation;
use App\Models\User;

class FormationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Formation $formation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Admins and professors can create formations
        if ($user->isAdmin()) {
            return true;
        }

        return isset($user->type) && in_array(strtolower($user->type), ['prof', 'professeur'], true);
    }

    public function update(User $user, Formation $formation): bool
    {
        // Admin can update any formation
        if ($user->isAdmin()) {
            return true;
        }

        // If user is an ecole (school), allow update only if the formation
        // is assigned to at least one class of that school
        if ($user->ecole) {
            return $formation->classes()->where('ecole_id', $user->ecole->id)->exists();
        }

        // Professors who own the formation (user_id) can update
        if ($formation->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Formation $formation): bool
    {
        // Only admin can delete formations
        return $user->isAdmin();
    }
}
