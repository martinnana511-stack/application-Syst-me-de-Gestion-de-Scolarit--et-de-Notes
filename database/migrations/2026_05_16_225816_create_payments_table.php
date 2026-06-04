<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete()
                  ->comment('Inscription concernée par ce paiement');
            $table->string('numero_recu', 30)->unique()
                  ->comment('Numéro de reçu généré automatiquement, ex: REC-2024-000001');
            $table->enum('type_paiement', ['inscription', 'scolarite', 'autre'])
                  ->default('scolarite');
            $table->decimal('montant_verse', 10, 2);
            $table->decimal('montant_du', 10, 2)
                  ->comment('Montant total dû au moment du paiement');
            $table->decimal('montant_restant', 10, 2)
                  ->comment('Calculé : montant_du - somme des versements précédents - montant_verse');
            $table->enum('mode_paiement', ['especes', 'cheque', 'mobile_money', 'virement'])
                  ->default('especes');
            $table->string('reference_externe', 100)->nullable()
                  ->comment('Numéro de chèque, transaction Mobile Money, etc.');
            $table->date('date_paiement');
            $table->text('observations')->nullable();
            $table->string('pdf_path')->nullable()
                  ->comment('Chemin du reçu PDF généré');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Gestionnaire ayant enregistré le paiement');
            $table->boolean('is_annule')->default(false);
            $table->text('motif_annulation')->nullable();
            $table->timestamps();

            $table->index(['enrollment_id', 'date_paiement']);
            $table->index('date_paiement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

