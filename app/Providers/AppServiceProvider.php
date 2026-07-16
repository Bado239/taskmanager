<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // 🚨 SÉCURITÉ ABSOLUE : Force la migration avant le chargement de n'importe quelle page
        // Si la table 'projects' est cassée ou inexistante, on réinstalle proprement.
        try {
            if (!Schema::hasTable('projects') || !Schema::hasColumn('projects', 'name')) {
                // Annuler toute transaction résiduelle
                while (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                Schema::disableForeignKeyConstraints();
                
                // On reconstruit tout à neuf de manière robuste
                Artisan::call('migrate:fresh', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
                
                Schema::enableForeignKeyConstraints();
            }
        } catch (\Exception $e) {
            // Évite de bloquer l'application si la DB n'est temporairement pas joignable
        }
    }
}