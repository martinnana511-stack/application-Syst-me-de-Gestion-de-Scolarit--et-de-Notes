<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{

    public function index(): View
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(20);
        return view('settings.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('settings.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => 'required|in:gestionnaire,enseignant',
            'telephone'  => 'nullable|string|max:20',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        User::create([
            ...$data,
            'password'  => Hash::make($data['password']),
            'is_active' => true,
        ]);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Compte créé avec succès.');
    }

    public function edit(User $user): View
    {
        return view('settings.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => "required|email|unique:users,email,{$user->id}",
            'role'      => 'required|in:gestionnaire,enseignant',
            'telephone' => 'nullable|string|max:20',
        ]);

        $user->update($data);

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Compte mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $user->delete();

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'Compte supprimé.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $status = $user->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Compte {$status}.");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe réinitialisé.');
    }
}
