<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'telephone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Accesseurs
    // -------------------------------------------------------------------------

    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /** Classes dont cet enseignant est titulaire */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    /** Paiements enregistrés par ce gestionnaire */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    /** Notes saisies par cet enseignant */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'created_by');
    }

    public function parentEleve(): HasOne
    {
        return $this->hasOne(ParentEleve::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeGestionnaires($query)
    {
        return $query->where('role', 'gestionnaire');
    }

    public function scopeEnseignants($query)
    {
        return $query->where('role', 'enseignant');
    }

    public function scopeParents($query)
    {
        return $query->where('role', 'parent');
    }

    public function scopeActifs($query)
    {
        return $query->where('is_active', true);
    }
}
