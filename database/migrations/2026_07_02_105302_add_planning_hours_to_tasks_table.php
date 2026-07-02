<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // On ajoute la colonne uniquement si elle n'existe pas déjà
            if (!Schema::hasColumn('tasks', 'heure_debut')) {
                $table->time('heure_debut')->nullable();
            }
            if (!Schema::hasColumn('tasks', 'heure_fin')) {
                $table->time('heure_fin')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'heure_debut')) {
                $table->dropColumn('heure_debut');
            }
            if (Schema::hasColumn('tasks', 'heure_fin')) {
                $table->dropColumn('heure_fin');
            }
        });
    }
};