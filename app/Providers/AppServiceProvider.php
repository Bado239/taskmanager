<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

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
        // Force l'exécution des migrations et du seeder de manière transparente au démarrage
        if (!app()->runningInConsole()) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            } catch (\Exception $e) {
                // Évite de bloquer l'application si la base est déjà prête
            }
        }
    }
}