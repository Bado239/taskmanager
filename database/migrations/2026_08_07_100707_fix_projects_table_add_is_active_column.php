<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Vérifie si la colonne n'existe pas avant de l'ajouter (sécurité)
            if (!Schema::hasColumn('projects', 'is_active')) {
                $table->boolean('is_active')->default(true)->comment('0=Archivé, 1=Actif');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // On ne supprime pas la colonne lors d'un rollback pour ne pas perdre de données
            // Mais si vous voulez la supprimer, commentez la ligne ci-dessus et décommentez celle du dessous
            // $table->dropColumn('is_active');
        });
    }
};