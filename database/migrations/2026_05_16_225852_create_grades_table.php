<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete()
                  ->comment('Inscription de l\'élève (lie student + class + academic_year)');
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('note', 5, 2)->nullable()->comment('Note obtenue');
            $table->unsignedTinyInteger('note_max')->default(20);
            $table->text('appreciation')->nullable()->comment('Commentaire de l\'enseignant');
            $table->boolean('is_absent')->default(false)->comment('Absent lors de l\'évaluation');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Enseignant ayant saisi la note');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Une seule note par élève, par matière, par trimestre
            $table->unique(['enrollment_id', 'subject_id', 'term_id'], 'uq_grade_per_term');
            $table->index(['term_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};

