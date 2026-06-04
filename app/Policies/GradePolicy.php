<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;
use App\Models\Term;

class GradePolicy
{
    /** Tout utilisateur actif peut consulter les notes */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Grade $grade): bool
    {
        return $user->is_active;
    }

    /**
     * Peut créer une note si :
     * - Le trimestre est encore ouvert (non clôturé)
     * - L'utilisateur est actif (gestionnaire ou enseignant)
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Peut modifier une note si :
     * - Le trimestre est encore ouvert
     * - L'utilisateur est actif
     * - Si enseignant : seulement ses propres notes (ou celles de sa classe)
     */
    public function update(User $user, Grade $grade): bool
    {
        if (! $user->is_active) return false;
        if ($grade->term->is_closed) return false;
        if ($user->isGestionnaire()) return true;

        // L'enseignant ne peut modifier que les notes qu'il a saisies
        return $grade->created_by === $user->id;
    }

    /** Seul le gestionnaire peut supprimer une note */
    public function delete(User $user, Grade $grade): bool
    {
        return $user->isGestionnaire()
            && $user->is_active
            && ! $grade->term->is_closed;
    }
}
