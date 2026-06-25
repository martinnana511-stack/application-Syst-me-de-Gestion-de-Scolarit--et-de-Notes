<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    // Toutes les notes groupées par trimestre avec moyennes
    public function index(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $enrollmentIds = Enrollment::where('student_id', $id)->pluck('id');

        $notes = Grade::whereIn('enrollment_id', $enrollmentIds)
                      ->with(['subject', 'term'])
                      ->orderBy('term_id')
                      ->get();

        // Grouper les notes par trimestre
        $trimestres = $notes->groupBy('term_id')->map(function ($notesParTrimestre) {
            $term = $notesParTrimestre->first()->term;
            $notesPresentes = $notesParTrimestre->where('is_absent', false);
            $moyenne = $notesPresentes->count() > 0
                ? round($notesPresentes->avg('note'), 2)
                : null;

            return [
                'trimestre_id'   => $term?->id,
                'trimestre_nom'  => $term?->libelle,
                'moyenne'        => $moyenne,
                'notes'          => $notesParTrimestre->map(function ($note) {
                    return [
                        'id'        => $note->id,
                        'matiere'   => $note->subject?->nom,
                        'note'      => $note->note,
                        'note_max'  => $note->note_max,
                        'mention'   => $note->mention,
                        'is_absent' => $note->is_absent,
                    ];
                })->values(),
            ];
        })->values();

        // Calculer la moyenne générale
        $toutesNotes = $notes->where('is_absent', false);
        $moyenneGenerale = $toutesNotes->count() > 0
            ? round($toutesNotes->avg('note'), 2)
            : null;

        return response()->json([
            'moyenne_generale' => $moyenneGenerale,
            'trimestres'       => $trimestres,
        ]);
    }

    // Notes par trimestre
    public function parTrimestre(Request $request, $id, $trimestre)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $enrollmentIds = Enrollment::where('student_id', $id)->pluck('id');

        $notes = Grade::whereIn('enrollment_id', $enrollmentIds)
                      ->where('term_id', $trimestre)
                      ->with(['subject', 'term'])
                      ->get();

        if ($notes->isEmpty()) {
            return response()->json(['message' => 'Aucune note trouvée pour ce trimestre.'], 404);
        }

        $notesPresentes = $notes->where('is_absent', false);
        $moyenne = $notesPresentes->count() > 0
            ? round($notesPresentes->avg('note'), 2)
            : null;

        return response()->json([
            'trimestre' => $notes->first()->term?->libelle,
            'moyenne'   => $moyenne,
            'notes'     => $notes->map(function ($note) {
                return [
                    'id'        => $note->id,
                    'matiere'   => $note->subject?->nom,
                    'note'      => $note->note,
                    'note_max'  => $note->note_max,
                    'mention'   => $note->mention,
                    'is_absent' => $note->is_absent,
                ];
            }),
        ]);
    }
}