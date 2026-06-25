<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('lien_parente', ['pere', 'mere', 'tuteur', 'autre'])->default('tuteur');
            $table->boolean('is_principal')->default(false); // contact principal
            $table->timestamps();

            // Un parent ne peut pas être lié deux fois au même élève
            $table->unique(['parent_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};