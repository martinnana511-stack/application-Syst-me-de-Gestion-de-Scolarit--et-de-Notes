<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stocke les moyennes calculées pour éviter de les recalculer à chaque affichage.
     * Ces données sont recalculées automatiquement à chaque modification de notes.
     */
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('moyenne_generale', 5, 2)->nullable()
                  ->comment('Moyenne générale pondérée par les coefficients');
            $table->unsignedSmallInteger('rang')->nullable()
                  ->comment('Rang dans la classe pour ce trimestre');
            $table->unsignedSmallInteger('effectif_classe')->nullable()
                  ->comment('Nombre d\'élèves classés ce trimestre');
            $table->string('mention', 30)->nullable()
                  ->comment('Excellent / Très bien / Bien / Passable / Insuffisant');
            $table->text('appreciation_conseil')->nullable()
                  ->comment('Appréciation générale du conseil de classe');
            $table->boolean('is_published')->default(false)
                  ->comment('Bulletin rendu visible aux parents');
            $table->timestamp('calculated_at')->nullable()
                  ->comment('Dernière date de calcul');
            $table->timestamps();

            $table->unique(['enrollment_id', 'term_id'], 'uq_report_card');
            $table->index(['term_id', 'moyenne_generale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};

