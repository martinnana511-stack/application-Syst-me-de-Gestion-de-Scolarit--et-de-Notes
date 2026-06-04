<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /** Affiche le formulaire de connexion */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     * Redirige vers le tableau de bord approprié selon le rôle.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Vérification du compte actif
        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administration.',
            ]);
        }

        // Redirection selon le rôle
        return redirect()->intended($this->redirectTo($user->role));
    }

    /** Déconnexion */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /** Détermine la route de redirection après connexion selon le rôle */
    private function redirectTo(string $role): string
    {
        return match($role) {
            'gestionnaire' => route('dashboard.index'),
            'enseignant'   => route('grades.index'),
            default        => route('dashboard.index'),
        };
    }
}
