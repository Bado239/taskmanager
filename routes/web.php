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

// Route d'archivage (Ajoutée ici)
Route::post('/tasks/{id}/archive', [TaskController::class, 'archive'])->name('tasks.archive');

// Sécurité anti-erreur 405 : si une requête GET arrive sur /tasks, on redirige vers le dashboard
Route::get('/tasks', function () {
    return redirect()->route('dashboard');
});

Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Route pour la suppression de projet
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

// Si vous gérez les projets dans TaskController :
Route::delete('/projects/{id}', [TaskController::class, 'destroyProject'])->name('projects.destroy');

// Route pour la suppression d'étape/catégorie si gérée dans le contrôleur
Route::delete('/categories/{id}', [TaskController::class, 'destroyCategory'])->name('categories.destroy');