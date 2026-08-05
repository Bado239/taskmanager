<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController; 

// Redirection de la racine
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes d'affichage (acceptent la méthode GET)
Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
Route::get('/tasks', [TaskController::class, 'index']);

// Actions sur les tâches
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Commutation de mode
Route::get('/switch-mode/{mode}', [TaskController::class, 'switchMode'])->name('mode.switch');
//le contrôleur est importé
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');