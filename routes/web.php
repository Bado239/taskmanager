<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
Route::get('/switch-mode/{mode}', [TaskController::class, 'switchMode'])->name('switch.mode');

Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Route Ajax appelée par Alpine.js
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');