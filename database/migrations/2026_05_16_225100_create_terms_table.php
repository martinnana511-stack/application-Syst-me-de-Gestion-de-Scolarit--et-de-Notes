<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->enum('numero', ['1', '2', '3'])->comment('Numéro du trimestre');
            $table->string('libelle', 30); // ex: "1er Trimestre"
            $table->date('date_debut');
            $table->date('date_fin');
            $table->boolean('is_closed')->default(false)->comment('Notes verrouillées');
            $table->timestamps();

            $table->unique(['academic_year_id', 'numero'], 'uq_term_per_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};

