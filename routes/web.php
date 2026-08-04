<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Redirection de la racine vers le dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Affichage du Dashboard et filtrage des vues (Dashboard, Office, Master)
Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

// Enregistrement et suppression des tâches
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Changement rapide de mode (office / master)
Route::get('/switch-mode/{mode}', [TaskController::class, 'switchMode'])->name('mode.switch');