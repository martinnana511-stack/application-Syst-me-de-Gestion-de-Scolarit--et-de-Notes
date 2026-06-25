<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    // Historique des paiements de l'élève
    public function index(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $eleve = $parent->students()->where('students.id', $id)->first();

        if (!$eleve) {
            return response()->json(['message' => 'Élève introuvable ou non autorisé.'], 404);
        }

        $enrollmentIds = Enrollment::where('student_id', $id)->pluck('id');

        $paiements = Payment::whereIn('enrollment_id', $enrollmentIds)
                            ->where('is_annule', false)
                            ->orderBy('date_paiement', 'desc')
                            ->get();

        $totalDu     = Enrollment::where('student_id', $id)->latest()->first()?->montant_du ?? 0;
        $totalPaye   = $paiements->sum('montant_verse');
        $resteAPayer = $paiements->first()?->montant_restant ?? 0;

        return response()->json([
            'total_du'      => $totalDu,
            'total_paye'    => $totalPaye,
            'reste_a_payer' => $resteAPayer,
            'paiements'     => $paiements->map(function ($p) {
                return [
                    'id'           => $p->id,
                    'numero_recu'  => $p->numero_recu,
                    'montant'      => $p->montant_verse,
                    'date'         => $p->date_paiement,
                    'mode'         => $p->mode_label,
                    'type'         => $p->type_label,
                ];
            }),
        ]);
    }

    // Reçu d'un paiement
    public function recu(Request $request, $id)
    {
        $parent = $request->user()->parentEleve;

        $enrollmentIds = Enrollment::whereHas('student.parents', function ($q) use ($parent) {
                            $q->where('parents.id', $parent->id);
                        })->pluck('id');

        $paiement = Payment::whereIn('enrollment_id', $enrollmentIds)
                           ->with('enrollment.student')
                           ->find($id);

        if (!$paiement) {
            return response()->json(['message' => 'Reçu introuvable ou non autorisé.'], 404);
        }

        $student = $paiement->enrollment?->student;

        return response()->json([
            'id'           => $paiement->id,
            'numero_recu'  => $paiement->numero_recu,
            'eleve'        => $student?->prenom . ' ' . $student?->nom,
            'montant'      => $paiement->montant_verse,
            'date' => $paiement->date_paiement?->format('d/m/Y'),
            'mode'         => $paiement->mode_label,
            'type'         => $paiement->type_label,
            'montant_du'   => $paiement->montant_du,
            'montant_restant' => $paiement->montant_restant,
        ]);
    }
}