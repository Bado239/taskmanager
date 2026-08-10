<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('personal_resources', function (Blueprint $table) {
            // Ajout du champ pour le contenu du livre/texte
            $table->longText('content')->nullable(); 
            
            // Ajout du champ pour les notes personnelles
            $table->longText('notes')->nullable();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_resources', function (Blueprint $table) {
            // Suppression des champs si on annule la migration
            $table->dropColumn(['content', 'notes']);
        });
    }
};