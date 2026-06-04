<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassSubject extends Pivot
{
    protected $table = 'class_subjects';

    public $incrementing = true;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'coefficient',
    ];

    protected function casts():array
    {
        return ['coefficient'=> 'decimal:2'];
    }

    // Relation accessibles depuis pivot
    public function schoolClass():BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject():BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher():BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
