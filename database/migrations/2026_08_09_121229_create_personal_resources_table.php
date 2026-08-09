<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_resources', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'lecture', 'vocabulaire', 'culture_g', 'citation'
            $table->string('title'); // Titre du livre, mot de vocabulaire, etc.
            $table->text('description')->nullable(); // Résumé, définition ou notes
            $table->string('status')->default('to_read'); // Pour les livres ('to_read', 'in_progress', 'completed')
            $table->string('author_or_source')->nullable(); // Auteur ou source
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_resources');
    }
};