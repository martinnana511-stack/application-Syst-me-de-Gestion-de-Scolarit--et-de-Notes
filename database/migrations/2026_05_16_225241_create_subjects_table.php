<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100); // ex: "Mathématiques", "Français", "Éducation Physique"
            $table->string('code', 10)->unique(); // ex: "MATH", "FR", "EPS"
            $table->decimal('coefficient', 4, 2)->default(1.00)
                  ->comment('Coefficient pour le calcul de la moyenne générale');
            $table->unsignedTinyInteger('note_max')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

