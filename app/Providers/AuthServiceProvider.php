<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Policies enregistrées (Model => Policy).
     */
    protected $policies = [
        \App\Models\Student::class    => \App\Policies\StudentPolicy::class,
        \App\Models\Payment::class    => \App\Policies\PaymentPolicy::class,
        \App\Models\Grade::class      => \App\Policies\GradePolicy::class,
        \App\Models\SchoolClass::class => \App\Policies\SchoolClassPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // -----------------------------------------------------------------
        // Gates globaux
        // -----------------------------------------------------------------

        /** Accès total à l'administration (gestion financière, config) */
        Gate::define('manage-admin', function (User $user): bool {
            return $user->isGestionnaire() && $user->is_active;
        });

        /** Accès au module pédagogique (notes, moyennes) */
        Gate::define('manage-pedagogy', function (User $user): bool {
            return $user->is_active; // gestionnaire ET enseignant
        });

        /** Saisie de notes : enseignant ou gestionnaire */
        Gate::define('enter-grades', function (User $user): bool {
            return $user->is_active;
        });

        /** Gestion des paiements : gestionnaire uniquement */
        Gate::define('manage-payments', function (User $user): bool {
            return $user->isGestionnaire() && $user->is_active;
        });

        /** Accès au tableau de bord global */
        Gate::define('view-dashboard', function (User $user): bool {
            return $user->isGestionnaire() && $user->is_active;
        });

        /** Gestion des utilisateurs (comptes admin/enseignants) */
        Gate::define('manage-users', function (User $user): bool {
            return $user->isGestionnaire() && $user->is_active;
        });

        /** Génération et annulation de reçus PDF */
        Gate::define('generate-receipts', function (User $user): bool {
            return $user->isGestionnaire() && $user->is_active;
        });

        /** Configuration de l'école (classes, frais, matières) */
        Gate::define('manage-settings', function (User $user): bool {
            return $user->isGestionnaire() && $user->is_active;
        });
    }
}
