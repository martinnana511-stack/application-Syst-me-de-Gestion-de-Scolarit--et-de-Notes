<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 20)->unique()->comment('Numéro d\'identification unique');
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance');
            $table->string('lieu_naissance', 100)->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->string('photo_path')->nullable()->comment('Chemin vers la photo stockée');
            $table->string('nom_pere', 150)->nullable();
            $table->string('nom_mere', 150)->nullable();
            $table->string('tuteur_nom', 150)->nullable()->comment('Tuteur légal si différent des parents');
            $table->string('tuteur_telephone', 20)->nullable();
            $table->string('tuteur_telephone2', 20)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->boolean('is_active')->default(true)->comment('Élève inscrit cette année');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nom', 'prenom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

