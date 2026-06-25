<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthParentController extends Controller
{
    // Connexion parent
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Vérifier si le compte existe et si le rôle est "parent"
        $user = User::where('email', $request->email)
                    ->where('role', 'parent')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.'
            ], 401);
        }

        // Vérifier si le compte est actif
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Votre compte a été désactivé. Veuillez contacter l\'administration.'
            ], 403);
        }

        $token = $user->createToken('parent-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    // Changer le mot de passe
    public function changePassword(Request $request)
    {
        $request->validate([
            'ancien_password'   => 'required',
            'nouveau_password'  => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->ancien_password, $request->user()->password)) {
            return response()->json([
                'message' => 'Ancien mot de passe incorrect.'
            ], 422);
        }

        $request->user()->update([
            'password' => Hash::make($request->nouveau_password)
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès.'
        ]);
    }

    // Profil du parent connecté
    public function profil(Request $request)
    {
        $user = $request->user()->load('parentEleve');

        return response()->json([
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'telephone'          => $user->telephone,
            'telephone_urgence'  => $user->parentEleve?->telephone_urgence,
            'profession'         => $user->parentEleve?->profession,
            'adresse'            => $user->parentEleve?->adresse,
        ]);
    }

    // Sauvegarder le token FCM
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $parent = $request->user()->parentEleve;

        if (!$parent) {
            return response()->json(['message' => 'Profil parent introuvable.'], 404);
        }

        $parent->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['message' => 'Token FCM sauvegardé.']);
    }    
}