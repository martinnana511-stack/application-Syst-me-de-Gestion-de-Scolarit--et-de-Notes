<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'term_id',
        'note',
        'note_max',
        'appreciation',
        'is_absent',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'note'      => 'decimal:2',
            'is_absent' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Accesseurs
    // -------------------------------------------------------------------------

    /** Note ramenée sur 20 */
    public function getNoteNormaliseeAttribute(): ?float
    {
        if ($this->is_absent || $this->note_max <= 0) return null;
        return round(($this->note / $this->note_max) * 20, 2);
    }

    /** Mention textuelle pour la note */
    public function getMentionAttribute(): string
    {
        $n = $this->note_normalisee;
        if ($n === null)  return 'Absent';
        if ($n >= 18)     return 'Excellent';
        if ($n >= 16)     return 'Très bien';
        if ($n >= 14)     return 'Bien';
        if ($n >= 12)     return 'Assez bien';
        if ($n >= 10)     return 'Passable';
        return 'Insuffisant';
    }

    /** Couleur Bootstrap associée à la mention */
    public function getCouleurAttribute(): string
    {
        $n = $this->note_normalisee;
        if ($n === null) return 'secondary';
        if ($n >= 14)   return 'success';
        if ($n >= 10)   return 'warning';
        return 'danger';
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePresents($query)
    {
        return $query->where('is_absent', false);
    }

    public function scopeForTerm($query, int $termId)
    {
        return $query->where('term_id', $termId);
    }

    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }
}
