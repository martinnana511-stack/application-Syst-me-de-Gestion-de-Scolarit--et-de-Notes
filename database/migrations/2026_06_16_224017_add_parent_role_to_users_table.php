<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier l'enum pour ajouter 'parent'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('gestionnaire', 'enseignant', 'parent') DEFAULT 'enseignant'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('gestionnaire', 'enseignant') DEFAULT 'enseignant'");
    }
};