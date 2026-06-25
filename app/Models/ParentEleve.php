<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentEleve extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parents'; // nom de la table en base

    protected $fillable = [
        'user_id',
        'telephone_urgence',
        'profession',
        'adresse',
        'fcm_token',
    ];

    // Un parent appartient à un user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un parent peut avoir plusieurs élèves
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
                    ->withPivot('lien_parente', 'is_principal')
                    ->withTimestamps();
    }

    public function annonces(): BelongsToMany
    {
        return $this->belongsToMany(Annonce::class, 'annonce_parent', 'parent_id', 'annonce_id')
                    ->withPivot('is_read')
                    ->withTimestamps();
    }
}