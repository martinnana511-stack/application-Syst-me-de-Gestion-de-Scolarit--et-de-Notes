<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\ParentEleve;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class NotificationController extends Controller
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
                           ->take(10)
                           ->get();

        return view('notifications.index', compact('annonces'));
    }

    public function envoyer(Request $request)
    {
        $request->validate([
            'titre'   => 'required|string|max:255',
            'message' => 'required|string',
            'cible'   => 'required|in:tous,annonce',
            'annonce_id' => 'nullable|exists:annonces,id',
        ]);

        // Récupérer tous les tokens FCM des parents actifs
        $tokens = ParentEleve::whereHas('user', function ($q) {
                                $q->where('is_active', true)
                                  ->where('role', 'parent');
                            })
                            ->whereNotNull('fcm_token')
                            ->pluck('fcm_token')
                            ->toArray();

        if (empty($tokens)) {
            return redirect()->route('notifications.index')
                             ->with('error', 'Aucun parent avec un token FCM disponible.');
        }

        // Envoyer les notifications
        $this->firebase->envoyerNotificationGroupe($tokens, $request->titre, $request->message);

        return redirect()->route('notifications.index')
                         ->with('success', count($tokens) . ' notification(s) envoyée(s) avec succès.');
    }
}