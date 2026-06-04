<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'school_classes';

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'niveau',
        'nom',
        'effectif_max',
        'frais_inscription',
        'frais_scolarite_annuel',
    ];

    protected function casts(): array
    {
        return [
            'frais_inscription'      => 'decimal:2',
            'frais_scolarite_annuel' => 'decimal:2',
        ];
    }

    // -------------------------------------------------------------------------
    // Accesseurs calculés
    // -------------------------------------------------------------------------

    /** Nombre d'élèves actuellement inscrits dans cette classe */
    public function getEffectifActuelAttribute(): int
    {
        return $this->enrollments()->where('statut', 'actif')->count();
    }

    /** Total des frais attendus pour cette classe */
    public function getTotalAttenduAttribute(): float
    {
        return $this->frais_scolarite_annuel * $this->getEffectifActuelAttribute();
    }

    /** Total des versements reçus pour cette classe */
    public function getTotalCollecteAttribute(): float
    {
        return Payment::whereHas('enrollment', fn($q) => $q->where('class_id', $this->id))
            ->where('is_annule', false)
            ->sum('montant_verse');
    }

    /** Reste à percevoir */
    public function getTotalRestantAttribute(): float
    {
        return $this->total_attendu - $this->total_collecte;
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    /** Élèves actifs dans cette classe */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id')
                    ->wherePivot('statut', 'actif')
                    ->withPivot(['date_inscription', 'statut'])
                    ->withTimestamps();
    }

    /** Matières enseignées dans cette classe */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
                    # ->using(ClassSubject::class)
                    ->withPivot(['teacher_id', 'coefficient'])
                    ->withTimestamps();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForCurrentYear($query)
    {
        $year = AcademicYear::active();
        return $year ? $query->where('academic_year_id', $year->id) : $query;
    }

    public function scopeNiveau($query, string $niveau)
    {
        return $query->where('niveau', $niveau);
    }
}
