<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /** Tout utilisateur actif peut consulter la liste des élèves */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /** Tout utilisateur actif peut voir le profil d'un élève */
    public function view(User $user, Student $student): bool
    {
        return $user->is_active;
    }

    /** Seul le gestionnaire peut inscrire un nouvel élève */
    public function create(User $user): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    /** Seul le gestionnaire peut modifier les données d'un élève */
    public function update(User $user, Student $student): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    /** Seul le gestionnaire peut supprimer (soft delete) un élève */
    public function delete(User $user, Student $student): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    /** Restauration d'un élève supprimé */
    public function restore(User $user, Student $student): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }
}
