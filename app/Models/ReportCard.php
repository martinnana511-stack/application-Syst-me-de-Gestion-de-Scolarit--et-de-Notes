<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'term_id',
        'moyenne_generale',
        'rang',
        'effectif_classe',
        'mention',
        'appreciation_conseil',
        'is_published',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'moyenne_generale' => 'decimal:2',
            'is_published'     => 'boolean',
            'calculated_at'    => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Calcul de la moyenne et du rang
    // -------------------------------------------------------------------------

    /**
     * Recalcule la moyenne générale pondérée pour cet enrollment + trimestre,
     * met à jour le rang dans la classe, puis sauvegarde.
     */
    public function recalculate(): self
    {
        $enrollment = $this->enrollment()->with('schoolClass.subjects')->first();

        // Calcul de la moyenne pondérée
        $result = DB::table('grades as g')
            ->join('subjects as s', 's.id', '=', 'g.subject_id')
            ->where('g.enrollment_id', $this->enrollment_id)
            ->where('g.term_id', $this->term_id)
            ->where('g.is_absent', false)
            ->selectRaw('
                SUM((g.note / g.note_max) * 20 * s.coefficient) as total_pondere,
                SUM(s.coefficient) as total_coeff
            ')
            ->first();

        $moyenne = ($result && $result->total_coeff > 0)
            ? round($result->total_pondere / $result->total_coeff, 2)
            : null;

        $this->moyenne_generale = $moyenne;
        $this->mention          = $moyenne !== null ? static::getMention($moyenne) : null;
        $this->calculated_at    = now();
        $this->save();

        // Recalcul des rangs pour toute la classe sur ce trimestre
        static::recalculateRanks($enrollment->class_id, $this->term_id);

        return $this->fresh();
    }

    /**
     * Recalcule les rangs de tous les élèves d'une classe pour un trimestre.
     */
    public static function recalculateRanks(int $classId, int $termId): void
    {
        $cards = static::whereHas('enrollment', fn($q) => $q->where('class_id', $classId))
            ->where('term_id', $termId)
            ->whereNotNull('moyenne_generale')
            ->orderByDesc('moyenne_generale')
            ->get();

        $effectif = $cards->count();
        $rang = 1;

        foreach ($cards as $card) {
            $card->update([
                'rang'            => $rang,
                'effectif_classe' => $effectif,
            ]);
            $rang++;
        }
    }

    /**
     * Détermine la mention à partir de la moyenne.
     */
    public static function getMention(float $moyenne): string
    {
        if ($moyenne >= 18)   return 'Excellent';
        if ($moyenne >= 16)   return 'Très bien';
        if ($moyenne >= 14)   return 'Bien';
        if ($moyenne >= 12)   return 'Assez bien';
        if ($moyenne >= 10)   return 'Passable';
        return 'Insuffisant';
    }

    /** Couleur Bootstrap pour l'affichage */
    public function getCouleurAttribute(): string
    {
        $m = (float) $this->moyenne_generale;
        if ($m >= 14) return 'success';
        if ($m >= 10) return 'warning';
        return 'danger';
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForTerm($query, int $termId)
    {
        return $query->where('term_id', $termId);
    }
}
