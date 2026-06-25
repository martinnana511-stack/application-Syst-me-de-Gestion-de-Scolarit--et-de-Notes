<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\ParentEleve;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    private FirebaseService $firebase;

    public function __construct()
    {
        $this->firebase = new FirebaseService();
    }

    public function index()
    {
        $annonces = Annonce::with('createdBy')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        return view('annonces.index', compact('annonces'));
    }

    public function create()
    {
        return view('annonces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'   => 'required|string|max:255',
            'contenu' => 'required|string',
            'type'    => 'required|in:general,examen,reunion,paiement,autre',
        ]);

        $annonce = Annonce::create([
            'titre'      => $request->titre,
            'contenu'    => $request->contenu,
            'type'       => $request->type,
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]);

        // Envoyer une notification push à tous les parents actifs
        $tokens = ParentEleve::whereHas('user', function ($q) {
                                $q->where('is_active', true)
                                  ->where('role', 'parent');
                            })
                            ->whereNotNull('fcm_token')
                            ->pluck('fcm_token')
                            ->toArray();

        if (!empty($tokens)) {
            $this->firebase->envoyerNotificationGroupe(
                $tokens,
                '📢 ' . $annonce->titre,
                $annonce->contenu
            );
        }

        return redirect()->route('annonces.index')
                         ->with('success', 'Annonce créée et notification envoyée aux parents.');
    }

    public function edit(Annonce $annonce)
    {
        return view('annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $request->validate([
            'titre'     => 'required|string|max:255',
            'contenu'   => 'required|string',
            'type'      => 'required|in:general,examen,reunion,paiement,autre',
            'is_active' => 'boolean',
        ]);

        $annonce->update([
            'titre'     => $request->titre,
            'contenu'   => $request->contenu,
            'type'      => $request->type,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('annonces.index')
                         ->with('success', 'Annonce modifiée avec succès.');
    }

    public function destroy(Annonce $annonce)
    {
        $annonce->delete();

        return redirect()->route('annonces.index')
                         ->with('success', 'Annonce supprimée avec succès.');
    }
}