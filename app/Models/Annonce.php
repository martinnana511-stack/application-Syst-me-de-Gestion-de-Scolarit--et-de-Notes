<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'type',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentEleve::class, 'annonce_parent', 'annonce_id', 'parent_id')
                    ->withPivot('is_read')
                    ->withTimestamps();
    }
}