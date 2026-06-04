<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Définit quelles matières sont enseignées dans quelle classe,
     * et quel enseignant les prend en charge.
     */
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Enseignant affecté à cette matière pour cette classe');
            $table->decimal('coefficient', 4, 2)->nullable()
                  ->comment('Surcharge du coefficient de la matière pour cette classe spécifique');
            $table->timestamps();

            $table->unique(['class_id', 'subject_id'], 'uq_class_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};

