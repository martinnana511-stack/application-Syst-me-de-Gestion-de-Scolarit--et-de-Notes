<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\ReportCard;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class GradeService
{
    /**
     * Recalcule la moyenne générale d'un élève pour un trimestre donné,
     * met à jour ou crée son ReportCard, puis recalcule les rangs de la classe.
     */
    public function recalculerMoyenne(int $enrollmentId, int $termId): ReportCard
    {
        // Calcul de la moyenne pondérée
        $result = DB::table('grades as g')
            ->join('subjects as s', 's.id', '=', 'g.subject_id')
            ->where('g.enrollment_id', $enrollmentId)
            ->where('g.term_id', $termId)
            ->where('g.is_absent', false)
            ->whereNotNull('g.note')
            ->selectRaw('
                SUM((g.note / g.note_max) * 20 * s.coefficient) AS total_pondere,
                SUM(s.coefficient)                               AS total_coeff,
                COUNT(*)                                         AS nb_notes
            ')
            ->first();

        $moyenne = null;
        if ($result && $result->total_coeff > 0 && $result->nb_notes > 0) {
            $moyenne = round($result->total_pondere / $result->total_coeff, 2);
        }

        $mention = $moyenne !== null ? ReportCard::getMention($moyenne) : null;

        // Mettre à jour ou créer le bulletin
        $reportCard = ReportCard::updateOrCreate(
            ['enrollment_id' => $enrollmentId, 'term_id' => $termId],
            [
                'moyenne_generale' => $moyenne,
                'mention'          => $mention,
                'calculated_at'    => now(),
            ]
        );

        // Recalcul des rangs pour la classe entière
        $enrollment = Enrollment::find($enrollmentId);
        if ($enrollment) {
            ReportCard::recalculateRanks($enrollment->class_id, $termId);
        }

        return $reportCard->fresh();
    }

    /**
     * Retourne le bulletin complet d'un élève avec le détail des notes par matière.
     */
    public function getBulletin(int $enrollmentId, int $termId): array
    {
        $grades = Grade::where('enrollment_id', $enrollmentId)
            ->where('term_id', $termId)
            ->with('subject')
            ->get()
            ->map(fn($g) => [
                'matiere'       => $g->subject->nom,
                'coefficient'   => $g->subject->coefficient,
                'note'          => $g->note,
                'note_max'      => $g->note_max,
                'note_sur_20'   => $g->note_normalisee,
                'mention'       => $g->mention,
                'couleur'       => $g->couleur,
                'appreciation'  => $g->appreciation,
                'is_absent'     => $g->is_absent,
            ]);

        $reportCard = ReportCard::where('enrollment_id', $enrollmentId)
            ->where('term_id', $termId)
            ->first();

        return [
            'grades'      => $grades,
            'reportCard'  => $reportCard,
        ];
    }

    /**
     * Recalcule les moyennes et rangs de toute une classe pour un trimestre.
     * Utile pour un recalcul global depuis le controller.
     */
    public function recalculerClasse(int $classId, int $termId): int
    {
        $enrollmentIds = Enrollment::where('class_id', $classId)
            ->actifs()
            ->pluck('id');

        foreach ($enrollmentIds as $id) {
            $this->recalculerMoyenne($id, $termId);
        }

        ReportCard::recalculateRanks($classId, $termId);

        return $enrollmentIds->count();
    }
}
