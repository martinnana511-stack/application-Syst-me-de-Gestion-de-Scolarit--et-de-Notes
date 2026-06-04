<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use App\Models\Subject;

class ClassSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $classes  = SchoolClass::all();
        $subjects = Subject::all();

        foreach ($classes as $classe) {
            foreach ($subjects as $subject) {
                $classe->subjects()->attach($subject->id, [
                    'coefficient' => $subject->coefficient,
                    'teacher_id'  => $classe->teacher_id,
                ]);
            }
        }

        echo "Matières associées aux classes !\n";
    }
}