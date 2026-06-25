<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annonce_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annonce_id')->constrained('annonces')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('parents')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->unique(['annonce_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annonce_parent');
    }
};