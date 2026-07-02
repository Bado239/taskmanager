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

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

Route::get('/force-clean-db', function () {
    try {
        // Force la réinitialisation complète des tables
        Artisan::call('migrate:fresh', ['--force' => true]);
        return "Base de données réinitialisée avec succès !";
    } catch (\Exception $e) {
        return "Erreur lors de la réinitialisation : " . $e->getMessage();
    }
});