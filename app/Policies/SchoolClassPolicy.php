<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;

class SchoolClassPolicy
{
    /** Tout utilisateur actif peut voir les classes */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, SchoolClass $schoolClass): bool
    {
        return $user->is_active;
    }

    /** Création, modification, suppression : gestionnaire uniquement */
    public function create(User $user): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    public function update(User $user, SchoolClass $schoolClass): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    public function delete(User $user, SchoolClass $schoolClass): bool
    {
        return $user->isGestionnaire()
            && $user->is_active
            && $schoolClass->enrollments()->actifs()->count() === 0;
    }
}
