<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\Grade;

class EleveController extends Controller
{
    // Liste des élèves du parent connecté
    public function index(Request $request)
    {
        $parent = $request->user()->parentEleve;

        if (!$parent) {
            return response()->json(['message' => 'Profil parent introuvable.'], 404);
        }

       $eleves = $parent->students()->get();

        return response()->json($eleves->map(function ($eleve) {
            return [
                'id'       => $eleve->id,
                'nom'      => $eleve->nom,
                'prenom'   => $eleve->prenom,
                // 'photo' => $eleve->photo_path ? str_replace('127.0.0.1', '10.0.2.2', asset('storage/' . $eleve->photo_path)) : null,
                'photo' => $eleve->photo_path ? str_replace('127.0.0.1', '10.17.105.112', asset('storage/' . $eleve->photo_path)) : null,
                'classe' => $eleve->enrollments()
                  ->where('statut', 'actif')
                  ->latest()
                  ->first()
                  ?->schoolClass?->nom ?? 'Non assigné',
                'lien'     => $eleve->pivot->lien_parente,
            ];
        }));
    }

    // Détail d'un élève
    public function show(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        return response()->json([
            'id'       => $eleve->id,
            'nom'      => $eleve->nom,
            'prenom'   => $eleve->prenom,
            'photo' => $eleve->photo_path ? str_replace('127.0.0.1', '10.0.2.2', asset('storage/' . $eleve->photo_path)) : null,
            'classe' => $eleve->enrollments()
              ->where('statut', 'actif')
              ->latest()
              ->first()
              ?->schoolClass?->nom ?? 'Non assigné',
            'lien'     => $eleve->pivot->lien_parente,
        ]);
    }

    // Moyenne générale de l'élève
    public function moyenne(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $enrollment = Enrollment::where('student_id', $id)->latest()->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Aucune inscription trouvée.'], 404);
        }

        return response()->json([
            'eleve_id'        => $id,
            'moyenne_generale' => $enrollment->general_average ?? null,
        ]);
    }

    // Rang de l'élève dans sa classe
    public function rang(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $enrollment = Enrollment::where('student_id', $id)
                                ->where('statut', 'actif')
                                ->latest()
                                ->first();

        if (!$enrollment) {
            return response()->json(['message' => 'Aucune inscription trouvée.'], 404);
        }

        // Calculer la moyenne de l'élève via ses notes
        $moyenneEleve = Grade::where('enrollment_id', $enrollment->id)
                            ->where('is_absent', false)
                            ->avg('note') ?? 0;

        // Récupérer toutes les inscriptions de la même classe
        $inscriptions = Enrollment::where('class_id', $enrollment->class_id)
                                ->where('academic_year_id', $enrollment->academic_year_id)
                                ->where('statut', 'actif')
                                ->pluck('id');

        // Calculer le rang
        $rang = 1;
        foreach ($inscriptions as $enrollmentId) {
            if ($enrollmentId == $enrollment->id) continue;
            $autreMoyenne = Grade::where('enrollment_id', $enrollmentId)
                                ->where('is_absent', false)
                                ->avg('note') ?? 0;
            if ($autreMoyenne > $moyenneEleve) {
                $rang++;
            }
        }

        $totalEleves = $inscriptions->count();

        return response()->json([
            'eleve_id'     => $id,
            'rang'         => $rang,
            'total_eleves' => $totalEleves,
            'moyenne'      => round($moyenneEleve, 2),
            'classe'       => $enrollment->schoolClass?->nom ?? 'Non assigné',
        ]);
    }

    // Résumé des dernières notes
    public function dernieresNotes(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $enrollmentIds = Enrollment::where('student_id', $id)->pluck('id');

        $notes = Grade::whereIn('enrollment_id', $enrollmentIds)
                    ->with(['subject', 'term'])
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();

        return response()->json($notes->map(function ($note) {
            return [
                'matiere'   => $note->subject?->nom,
                'note'      => $note->note,
                'note_max'  => $note->note_max,
                'mention'   => $note->mention,
                'trimestre' => $note->term?->libelle,
            ];
        }));
    }
}