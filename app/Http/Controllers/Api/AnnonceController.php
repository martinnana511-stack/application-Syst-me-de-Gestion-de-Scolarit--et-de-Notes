<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    // Liste de toutes les annonces avec statut de lecture
    public function index(Request $request)
    {
        $parent = $request->user()->parentEleve;

        $annonces = Annonce::where('is_active', true)
                           ->orderBy('created_at', 'desc')
                           ->get();

        // Récupérer les annonces lues par ce parent
        $annoncesLues = $parent ? $parent->annonces()->pluck('annonce_id')->toArray() : [];

        $nonLus = 0;

        $data = $annonces->map(function ($a) use ($annoncesLues, &$nonLus) {
            $isRead = in_array($a->id, $annoncesLues);
            if (!$isRead) $nonLus++;

            return [
                'id'      => $a->id,
                'titre'   => $a->titre,
                'contenu' => $a->contenu,
                'type'    => $a->type,
                'date'    => $a->created_at->format('d/m/Y H:i'),
                'is_read' => $isRead,
            ];
        });

        return response()->json([
            'total'    => $annonces->count(),
            'non_lus'  => $nonLus,
            'annonces' => $data,
        ]);
    }

    // Détail d'une annonce + marquer comme lue
    public function show(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $annonce = Annonce::find($id);

        if (!$annonce) {
            return response()->json(['message' => 'Annonce introuvable.'], 404);
        }

        // Marquer comme lue
        if ($parent) {
            $parent->annonces()->syncWithoutDetaching([
                $annonce->id => ['is_read' => true]
            ]);
        }

        return response()->json([
            'id'      => $annonce->id,
            'titre'   => $annonce->titre,
            'contenu' => $annonce->contenu,
            'type'    => $annonce->type,
            'date'    => $annonce->created_at->format('d/m/Y H:i'),
            'is_read' => true,
        ]);
    }
}