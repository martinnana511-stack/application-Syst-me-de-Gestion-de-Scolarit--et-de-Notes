<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthParentController;
use App\Http\Controllers\Api\EleveController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\AnnonceController;

// ---------------------------------------------------------------
// Routes publiques (sans token)
// ---------------------------------------------------------------
Route::prefix('parent')->group(function () {
    Route::post('/login', [AuthParentController::class, 'login']);
});

// ---------------------------------------------------------------
// Routes protégées (token Sanctum obligatoire)
// ---------------------------------------------------------------
Route::middleware('auth:sanctum')->prefix('parent')->group(function () {

    // Authentification
    Route::post('/logout', [AuthParentController::class, 'logout']);
    Route::put('/password', [AuthParentController::class, 'changePassword']);
    Route::get('/profil', [AuthParentController::class, 'profil']);

    // Élèves du parent
    Route::get('/eleves', [EleveController::class, 'index']);
    Route::get('/eleves/{id}', [EleveController::class, 'show']);
    Route::get('/eleves/{id}/moyenne', [EleveController::class, 'moyenne']);
    Route::get('/eleves/{id}/rang', [EleveController::class, 'rang']);

    // Notes
    Route::get('/eleves/{id}/notes', [NoteController::class, 'index']);
    Route::get('/eleves/{id}/notes/{trimestre}', [NoteController::class, 'parTrimestre']);

    // Paiements
    Route::get('/eleves/{id}/paiements', [PaiementController::class, 'index']);
    Route::get('/paiements/{id}/recu', [PaiementController::class, 'recu']);

    // Absences
    Route::get('/eleves/{id}/absences', [AbsenceController::class, 'index']);

    // Annonces
    Route::get('/annonces', [AnnonceController::class, 'index']);
    Route::get('/annonces/{id}', [AnnonceController::class, 'show']);

    // Token FCM
    Route::post('/fcm-token', [AuthParentController::class, 'saveFcmToken']);

    // Messages
    Route::get('/messages', [App\Http\Controllers\Api\MessageController::class, 'index']);
    Route::put('/messages/{id}/read', [App\Http\Controllers\Api\MessageController::class, 'markAsRead']);

    // Dernières notes
    Route::get('/eleves/{id}/dernieres-notes', [EleveController::class, 'dernieresNotes']);
});