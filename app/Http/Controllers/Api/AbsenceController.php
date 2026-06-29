<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    // Liste des absences de l'élève
    public function index(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $absences = Absence::where('student_id', $id)
                           ->orderBy('date', 'desc')
                           ->get();

        return response()->json([
            'total'    => $absences->count(),
            'absences' => $absences->map(function ($a) {
                return [
                    'id'      => $a->id,
                    'date'    => $a->date->format('d/m/Y'),
                    'motif'   => $a->motif ?? 'Non renseigné',
                    'justifie'=> $a->justifie,
                ];
            }),
        ]);
    }
}