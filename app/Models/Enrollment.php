<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'date_inscription',
        'statut',
        'motif_depart',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_inscription' => 'date',
        ];
    }

    // -------------------------------------------------------------------------
    // Accesseurs
    // -------------------------------------------------------------------------

    /** Montant total versé pour cette inscription */
    public function getMontantPayeAttribute(): float
    {
        return $this->payments()->where('is_annule', false)->sum('montant_verse');
    }

    /** Reste à payer */
    public function getResteAttribute(): float
    {
        $frais = $this->schoolClass?->frais_scolarite_annuel ?? 0;
        return max(0, $frais - $this->montant_paye);
    }

    /** Pourcentage payé */
    public function getPourcentagePayeAttribute(): float
    {
        $frais = $this->schoolClass?->frais_scolarite_annuel ?? 0;
        if ($frais <= 0) return 100;
        return min(100, round(($this->montant_paye / $frais) * 100, 1));
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeImpayes($query)
    {
        return $query->whereHas('payments', null, '<', 1)
                     ->orWhereHas('schoolClass', function ($q) {
                         $q->whereColumn(
                             'frais_scolarite_annuel', '>',
                             \DB::raw('(SELECT COALESCE(SUM(montant_verse),0) FROM payments
                                        WHERE enrollment_id = enrollments.id AND is_annule = 0)')
                         );
                     });
    }

    public function scopeForCurrentYear($query)
    {
        $year = AcademicYear::active();
        return $year ? $query->where('academic_year_id', $year->id) : $query;
    }
}
