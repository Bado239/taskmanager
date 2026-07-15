<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Page d'accueil : redirige automatiquement vers les tâches du jour
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// 📊 Tableau de bord avec indicateurs cliquables
Route::get('/tasks/dashboard', [TaskController::class, 'dashboard'])->name('tasks.dashboard');

// 📋 CRUD des tâches (index, create, store, edit, update, destroy)
Route::resource('tasks', TaskController::class);

Route::get('/veille-tech', [App\Http\Controllers\TaskController::class, 'veilleTech'])->name('tasks.veille');

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

Route::get('/force-clean-db', function () {
    try {
        // 1. Force la réinitialisation complète des tables
        Artisan::call('migrate:fresh', ['--force' => true]);

        // 2. AJOUT : Force l'exécution du DatabaseSeeder avec les nouvelles modalités
        Artisan::call('db:seed', ['--force' => true]);

        return "Base de données réinitialisée et repeuplée avec succès !";
    } catch (\Exception $e) {
        return "Erreur lors de la réinitialisation : " . $e->getMessage();
    }
});
// 🚀 ROUTE TEMPORAIRE DE MIGRATION DE BASE DE DONNÉES
Route::get('/run-migration-bado', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migration réussie ! Le résultat de la console : <br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Erreur lors de la migration : " . $e->getMessage();
    }
});

use App\Models\Schedule;
use Illuminate\Http\Request;

Route::post('/schedule-upload', function (Request $request) {
    $request->validate([
        'schedule_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    if ($request->hasFile('schedule_file')) {
        $path = $request->schedule_file->store('schedules', 'public');
        
        // On enregistre le nouveau chemin
        Schedule::create(['file_path' => $path]);
    }

    return back()->with('success', 'Emploi du temps mis à jour avec succès !');
})->name('schedule.upload');