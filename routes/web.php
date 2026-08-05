<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');

// Route pour basculer le mode (office / master)
Route::get('/switch-mode/{mode}', [TaskController::class, 'switchMode'])->name('mode.switch');

// Routes pour les tâches
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// Sécurité anti-erreur 405 : si une requête GET arrive sur /tasks, on redirige vers le dashboard
Route::get('/tasks', function () {
    return redirect()->route('dashboard');
});

Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Route pour la suppression de projet
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');