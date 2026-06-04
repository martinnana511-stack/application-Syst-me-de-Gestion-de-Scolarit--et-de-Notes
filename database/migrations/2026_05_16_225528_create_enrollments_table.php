<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table pivot entre students et classes.
     * Permet de suivre l'historique des inscriptions d'un élève sur plusieurs années.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('date_inscription');
            $table->enum('statut', ['actif', 'transfere', 'exclu', 'diplome'])->default('actif');
            $table->string('motif_depart', 255)->nullable()->comment('Motif si transfert ou exclusion');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Gestionnaire ayant effectué l\'inscription');
            $table->timestamps();

            // Un élève ne peut être inscrit qu'une fois par année scolaire
            $table->unique(['student_id', 'academic_year_id'], 'uq_enrollment_per_year');
            $table->index(['class_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
