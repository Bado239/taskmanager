<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PersonalResourceController;

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

// Protection si quelqu'un ouvre directement /personal-resources en GET
Route::get('/personal-resources', function () {
    return redirect()->route('dashboard', [
        'view' => 'personal'
    ]);
});


// Ajout livre PDF
Route::post('/personal-resources', [PersonalResourceController::class, 'store'])
    ->name('personal-resources.store');


// Suppression livre
Route::delete('/personal-resources/{id}', [PersonalResourceController::class, 'destroy'])
    ->name('personal-resources.destroy');


// Route pour ouvrir le livre en mode lecture
Route::get('/personal-resources/{id}', [PersonalResourceController::class, 'show'])->name('personal-resources.show');

// Route pour sauvegarder le contenu et les notes
Route::put('/personal-resources/{id}', [PersonalResourceController::class, 'update'])->name('personal-resources.update');

// Sauvegarde automatique de la progression de lecture PDF
Route::post(
    '/personal-resources/{id}/progress',
    [PersonalResourceController::class, 'progress']
)
->name('personal-resources.progress');