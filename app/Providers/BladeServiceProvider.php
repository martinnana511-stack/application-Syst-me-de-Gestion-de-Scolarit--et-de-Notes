<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Directives Blade personnalisées pour le contrôle d'accès dans les vues.
 *
 * Usage dans les templates :
 *
 *   @gestionnaire
 *       <a href="{{ route('payments.create') }}">Enregistrer un paiement</a>
 *   @endgestionnaire
 *
 *   @enseignant
 *       <span>Bienvenue, enseignant</span>
 *   @endenseignant
 *
 *   @role('gestionnaire', 'enseignant')
 *       Contenu visible par les deux rôles
 *   @endrole
 */
class BladeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // @gestionnaire ... @endgestionnaire
        Blade::if('gestionnaire', function (): bool {
            return auth()->check() && auth()->user()->isGestionnaire();
        });

        // @enseignant ... @endenseignant
        Blade::if('enseignant', function (): bool {
            return auth()->check() && auth()->user()->isEnseignant();
        });

        // @role('gestionnaire', 'enseignant') ... @endrole
        Blade::if('role', function (string ...$roles): bool {
            return auth()->check() && in_array(auth()->user()->role, $roles, true);
        });

        // @active — affiche le contenu seulement si le compte est actif
        Blade::if('active', function (): bool {
            return auth()->check() && auth()->user()->is_active;
        });

        // @can('gate') est déjà fourni par Laravel, mais on ajoute @canGate
        // pour rester cohérent avec la nomenclature du projet
    }

    public function register(): void {}
}
