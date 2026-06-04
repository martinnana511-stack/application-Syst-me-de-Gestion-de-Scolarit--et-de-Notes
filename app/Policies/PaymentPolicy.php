<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /** Gestionnaire uniquement pour la liste des paiements */
    public function viewAny(User $user): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }

    /** Un paiement ne peut pas être modifié, seulement annulé */
    public function update(User $user, Payment $payment): bool
    {
        return false;
    }

    /** Seul le gestionnaire peut annuler un paiement */
    public function cancel(User $user, Payment $payment): bool
    {
        return $user->isGestionnaire()
            && $user->is_active
            && ! $payment->is_annule;
    }

    /** Génération du reçu PDF */
    public function generatePdf(User $user, Payment $payment): bool
    {
        return $user->isGestionnaire() && $user->is_active;
    }
}
