<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'coefficient',
        'note_max',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'coefficient' => 'decimal:2',
            'is_active'   => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /** Classes qui enseignent cette matière */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id')
                    ->using(ClassSubject::class)
                    ->withPivot(['teacher_id', 'coefficient'])
                    ->withTimestamps();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActives($query)
    {
        return $query->where('is_active', true);
    }
}
