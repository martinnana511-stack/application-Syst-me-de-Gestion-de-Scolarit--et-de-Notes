<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Enseignant titulaire');
            $table->enum('niveau', ['CP1', 'CP2', 'CE1', 'CE2', 'CM1', 'CM2']);
            $table->string('nom', 50)->comment('ex: CP1-A, CP1-B');
            $table->unsignedSmallInteger('effectif_max')->default(40);
            $table->decimal('frais_inscription', 10, 2)->default(0)
                  ->comment('Frais unique à l\'inscription');
            $table->decimal('frais_scolarite_annuel', 10, 2)->default(0)
                  ->comment('Frais de scolarité pour toute l\'année');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_year_id', 'nom'], 'uq_class_name_per_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};

