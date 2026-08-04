<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Redirection de la racine vers /tasks
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route principale du Dashboard
Route::get('/tasks', [TaskController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [TaskController::class, 'index'])->name('tasks.index');

// Enregistrement et suppression des tâches
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Changement de mode (office / master)
Route::get('/switch-mode/{mode}', [TaskController::class, 'switchMode'])->name('mode.switch');