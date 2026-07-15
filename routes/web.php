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

// 🚀 ROUTE DE MIGRATION DE BASE DE DONNÉES ET CRÉATION DU DOSSIER DE STOCKAGE PUBLIC
Route::get('/run-migration-bado', function () {
    try {
        // Exécute les nouvelles tables (dont 'schedules')
        Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = Artisan::output();

        // Crée le lien symbolique vers le stockage public indispensable pour Render !
        Artisan::call('storage:link');
        $storageOutput = Artisan::output();

        return "<h3>Migration et liaison de stockage réussies !</h3>
                <p>Résultat des migrations :</p>
                <pre>" . $migrationOutput . "</pre>
                <p>Résultat du stockage :</p>
                <pre>" . ($storageOutput ?: 'Lien de stockage déjà existant ou lié.') . "</pre>";
    } catch (\Exception $e) {
        return "Erreur lors de la maintenance : " . $e->getMessage();
    }
});


// 🖼️ Enregistrement de l'emploi du temps
Route::post('/schedule-upload', function (Request $request) {
    $request->validate([
        'schedule_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    if ($request->hasFile('schedule_file')) {
        // Sauvegarde physique de l'image
        $path = $request->file('schedule_file')->store('schedules', 'public');
        
        // On enregistre le nouveau chemin en base de données
        Schedule::create(['file_path' => $path]);
    }

    return back()->with('success', 'Emploi du temps mis à jour avec succès !');
})->name('schedule.upload');