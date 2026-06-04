<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'photo_path',
        'nom_pere',
        'nom_mere',
        'tuteur_nom',
        'tuteur_telephone',
        'tuteur_telephone2',
        'adresse',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'is_active'      => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Hooks
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (empty($student->matricule)) {
                $student->matricule = static::generateMatricule();
            }
        });
    }

    // -------------------------------------------------------------------------
    // Génération du matricule
    // -------------------------------------------------------------------------

    public static function generateMatricule(): string
    {
        $year   = date('Y');
        $prefix = 'EL-' . $year . '-';
        $last   = static::where('matricule', 'like', $prefix . '%')
                        ->orderByDesc('matricule')
                        ->value('matricule');

        $num = $last ? (intval(substr($last, -5)) + 1) : 1;
        return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Accesseurs
    // -------------------------------------------------------------------------

    /** Nom complet : NOM Prénom */
    public function getNomCompletAttribute(): string
    {
        return strtoupper($this->nom) . ' ' . ucfirst(strtolower($this->prenom));
    }

    /** URL de la photo ou image par défaut */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : asset('images/avatar-default.png');
    }

    /** Âge calculé */
    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /** Toutes les inscriptions de cet élève (toutes années) */
    public function enrollments(): HasMany
    {
        return $this->HasMany(Enrollment::class);
    }

    /** Inscription de l'année scolaire active */
    public function currentEnrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class)
                    ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
                    ->where('statut', 'actif');
    }

    /** Classe actuelle via l'inscription en cours */
    public function currentClass(): ?SchoolClass
    {
        return $this->currentEnrollment?->schoolClass;
    }

    /** Toutes les classes fréquentées */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'enrollments', 'student_id', 'class_id')
                    ->withPivot(['date_inscription', 'statut'])
                    ->withTimestamps();
    }

    /** Tous les paiements via les inscriptions */

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Enrollment::class);
    }

    /** Toutes les notes via les inscriptions */

    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(Grade::class, Enrollment::class);
    }

    // -------------------------------------------------------------------------
    // Méthodes métier
    // -------------------------------------------------------------------------

    /** Montant total payé pour l'année en cours */
    public function totalPaye(): float
    {
        return $this->currentEnrollment
            ? Payment::where('enrollment_id', $this->currentEnrollment->id)
                     ->where('is_annule', false)
                     ->sum('montant_verse')
            : 0;
    }

    /** Reste à payer pour l'année en cours */
    public function resteAPayer(): float
    {
        $frais = $this->currentEnrollment?->schoolClass?->frais_scolarite_annuel ?? 0;
        return max(0, $frais - $this->totalPaye());
    }

    /** Vérifie si l'élève est en retard de paiement */
    public function hasImpayes(): bool
    {
        return $this->resteAPayer() > 0;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nom', 'like', "%{$term}%")
              ->orWhere('prenom', 'like', "%{$term}%")
              ->orWhere('matricule', 'like', "%{$term}%");
        });
    }
}
