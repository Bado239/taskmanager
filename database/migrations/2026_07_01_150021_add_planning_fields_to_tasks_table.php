<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            // Ordre d'exécution
            if (!Schema::hasColumn('tasks', 'ordre')) {
                $table->integer('ordre')->default(999)->after('priority');
            }

            // Date prévue
            if (!Schema::hasColumn('tasks', 'date_prevue')) {
                $table->date('date_prevue')->nullable()->after('ordre');
            }

            // Heure de début
            if (!Schema::hasColumn('tasks', 'heure_debut')) {
                $table->time('heure_debut')->nullable()->after('date_prevue');
            }

            // Heure de fin
            if (!Schema::hasColumn('tasks', 'heure_fin')) {
                $table->time('heure_fin')->nullable()->after('heure_debut');
            }

            // Durée estimée en minutes
            if (!Schema::hasColumn('tasks', 'duree_estimee')) {
                $table->integer('duree_estimee')->nullable()->after('heure_fin');
            }

        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            if (Schema::hasColumn('tasks', 'ordre')) {
                $table->dropColumn('ordre');
            }

            if (Schema::hasColumn('tasks', 'date_prevue')) {
                $table->dropColumn('date_prevue');
            }

            if (Schema::hasColumn('tasks', 'heure_debut')) {
                $table->dropColumn('heure_debut');
            }

            if (Schema::hasColumn('tasks', 'heure_fin')) {
                $table->dropColumn('heure_fin');
            }

            if (Schema::hasColumn('tasks', 'duree_estimee')) {
                $table->dropColumn('duree_estimee');
            }

        });
    }
};