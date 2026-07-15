<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\Schedule;
use Illuminate\Http\Request;

// Page d'accueil : redirige automatiquement vers les tâches du jour
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// 📊 Tableau de bord avec indicateurs cliquables
Route::get('/tasks/dashboard', [TaskController::class, 'dashboard'])->name('tasks.dashboard');

// 📋 CRUD des tâches (index, create, store, edit, update, destroy)
Route::resource('tasks', TaskController::class);

// 📰 Page de Veille Technologique
Route::get('/veille-tech', [TaskController::class, 'veilleTech'])->name('tasks.veille');


// 🧰 Outils de Maintenance / Réinitialisation
Route::get('/force-clean-db', function () {
    try {
        // 1. Force la réinitialisation complète des tables
        Artisan::call('migrate:fresh', ['--force' => true]);

        // 2. Force l'exécution du DatabaseSeeder avec les nouvelles modalités
        Artisan::call('db:seed', ['--force' => true]);

        return "Base de données réinitialisée et repeuplée avec succès !";
    } catch (\Exception $e) {
        return "Erreur lors de la réinitialisation : " . $e->getMessage();
    }
});

// 🚀 ROUTE DE MIGRATION DE BASE DE DONNÉES
Route::get('/run-migration-bado', function () {
    try {
        // Exécute les nouvelles tables (dont 'schedules')
        Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = Artisan::output();

        return "<h3>Migration réussie !</h3>
                <p>Résultat des migrations :</p>
                <pre>" . $migrationOutput . "</pre>";
    } catch (\Exception $e) {
        return "Erreur lors de la maintenance : " . $e->getMessage();
    }
});


// 🖼️ Enregistrement du lien de l'emploi du temps (Compatible Render Gratuit ☁️)
Route::post('/schedule-upload', function (Request $request) {
    // On valide que l'entrée est bien une URL valide
    $request->validate([
        'schedule_url' => 'required|url',
    ]);

    // On enregistre simplement l'adresse URL externe de l'image
    Schedule::create([
        'file_path' => $request->schedule_url
    ]);

    return back()->with('success', 'Emploi du temps mis à jour avec succès !');
})->name('schedule.upload');