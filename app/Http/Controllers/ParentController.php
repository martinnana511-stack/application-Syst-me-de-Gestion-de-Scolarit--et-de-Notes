<?php

namespace App\Http\Controllers;

use App\Models\ParentEleve;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index()
    {
        $parents = ParentEleve::with(['user', 'students'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);

        return view('parents.index', compact('parents'));
    }

    public function create(Request $request)
    {
        $query = Student::actifs()->orderBy('nom');

        // Filtre par classe
        if ($request->filled('class_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('statut', 'actif');
            });
        }

        // Filtre par nom
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->get();
        $classes = SchoolClass::orderBy('niveau')->get();

        return view('parents.create', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:8|confirmed',
            'telephone'         => 'nullable|string|max:20',
            'telephone_urgence' => 'nullable|string|max:20',
            'profession'        => 'nullable|string|max:255',
            'adresse'           => 'nullable|string|max:255',
            'students'          => 'nullable|array',
            'students.*'        => 'exists:students,id',
            'lien_parente'      => 'nullable|array',
        ]);

        // Créer le compte utilisateur
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'parent',
            'telephone' => $request->telephone,
            'is_active' => true,
        ]);

        // Créer le profil parent
        $parent = ParentEleve::create([
            'user_id'           => $user->id,
            'telephone_urgence' => $request->telephone_urgence,
            'profession'        => $request->profession,
            'adresse'           => $request->adresse,
        ]);

        // Lier les élèves
        if ($request->has('students') && is_array($request->students)) {
            foreach ($request->students as $studentId) {
                $lien = $request->lien_parente[$studentId] ?? 'tuteur';
                $parent->students()->attach($studentId, [
                    'lien_parente' => $lien,
                    'is_principal' => true,
                ]);
            }
        }

        return redirect()->route('parents.index')
                         ->with('success', 'Compte parent créé avec succès.');
    }

    public function show(ParentEleve $parent)
    {
        $parent->load(['user', 'students']);
        return view('parents.show', compact('parent'));
    }

    public function edit(Request $request, ParentEleve $parent)
    {
        $parent->load(['user', 'students']);

        $query = Student::actifs()->orderBy('nom');

        // Filtre par classe
        if ($request->filled('class_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('statut', 'actif');
            });
        }

        // Filtre par nom
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->get();
        $classes = SchoolClass::orderBy('niveau')->get();

        return view('parents.edit', compact('parent', 'students', 'classes'));
    }

    public function update(Request $request, ParentEleve $parent)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $parent->user_id,
            'telephone'         => 'nullable|string|max:20',
            'telephone_urgence' => 'nullable|string|max:20',
            'profession'        => 'nullable|string|max:255',
            'adresse'           => 'nullable|string|max:255',
            'students'          => 'nullable|array',
            'students.*'        => 'exists:students,id',
            'lien_parente'      => 'nullable|array',
        ]);

        // Mettre à jour le compte utilisateur
        $parent->user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ]);

        // Mettre à jour le profil parent
        $parent->update([
            'telephone_urgence' => $request->telephone_urgence,
            'profession'        => $request->profession,
            'adresse'           => $request->adresse,
        ]);

        // Mettre à jour les élèves liés
        $sync = [];
        if ($request->has('students') && is_array($request->students)) {
            foreach ($request->students as $studentId) {
                $lien = $request->lien_parente[$studentId] ?? 'tuteur';
                $sync[$studentId] = [
                    'lien_parente' => $lien,
                    'is_principal' => true,
                ];
            }
        }
        $parent->students()->sync($sync);

        return redirect()->route('parents.index')
                        ->with('success', 'Compte parent modifié avec succès.');
    }

    public function destroy(ParentEleve $parent)
    {
        $parent->user->delete();
        $parent->delete();

        return redirect()->route('parents.index')
                         ->with('success', 'Compte parent supprimé avec succès.');
    }

    public function toggleActive(ParentEleve $parent)
    {
        $parent->user->update([
            'is_active' => !$parent->user->is_active,
        ]);

        $status = $parent->user->is_active ? 'activé' : 'désactivé';

        return redirect()->route('parents.index')
                         ->with('success', "Compte parent {$status} avec succès.");
    }

    public function notifierForm(ParentEleve $parent)
    {
        $parent->load('user');
        return view('parents.notifier', compact('parent'));
    }

    public function notifierEnvoyer(Request $request, ParentEleve $parent)
    {
        $request->validate([
            'titre'   => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $parent->load('user');

        // Sauvegarder le message en base de données
        \App\Models\Message::create([
            'parent_id'  => $parent->id,
            'created_by' => auth()->id(),
            'titre'      => $request->titre,
            'contenu'    => $request->message,
            'is_read'    => false,
        ]);

        // Envoyer la notification push si token disponible
        if (!empty($parent->fcm_token)) {
            $firebase = new \App\Services\FirebaseService();
            $firebase->envoyerNotification(
                $parent->fcm_token,
                $request->titre,
                $request->message
            );
        }

        return redirect()->route('parents.index')
                        ->with('success', 'Message envoyé à ' . $parent->user->name . ' avec succès.');
    }
}