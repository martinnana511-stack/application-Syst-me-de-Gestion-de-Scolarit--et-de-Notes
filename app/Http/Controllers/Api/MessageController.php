<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Liste des messages du parent connecté
    public function index(Request $request)
    {
        $parent = $request->user()->parentEleve;

        if (!$parent) {
            return response()->json(['message' => 'Profil parent introuvable.'], 404);
        }

        $messages = Message::where('parent_id', $parent->id)
                           ->with('createdBy')
                           ->orderBy('created_at', 'desc')
                           ->get();

        return response()->json([
            'total'        => $messages->count(),
            'non_lus'      => $messages->where('is_read', false)->count(),
            'messages'     => $messages->map(function ($m) {
                return [
                    'id'         => $m->id,
                    'titre'      => $m->titre,
                    'contenu'    => $m->contenu,
                    'is_read'    => $m->is_read,
                    'date'       => $m->created_at->format('d/m/Y H:i'),
                    'envoye_par' => $m->createdBy?->name ?? 'Administration',
                ];
            }),
        ]);
    }

    // Marquer un message comme lu
    public function markAsRead(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $message = Message::where('parent_id', $parent->id)->find($id);

        if (!$message) {
            return response()->json(['message' => 'Message introuvable.'], 404);
        }

        $message->update(['is_read' => true]);

        return response()->json(['message' => 'Message marqué comme lu.']);
    }
}