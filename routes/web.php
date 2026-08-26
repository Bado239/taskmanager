<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PersonalResourceController;


/*
|--------------------------------------------------------------------------
| Accueil
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return redirect()->route('dashboard');

});



/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [
    TaskController::class,
    'index'
])->name('dashboard');



/*
|--------------------------------------------------------------------------
| Changement de mode
|--------------------------------------------------------------------------
*/

Route::get('/switch-mode/{mode}', [
    TaskController::class,
    'switchMode'
])->name('mode.switch');



/*
|--------------------------------------------------------------------------
| Gestion des tâches
|--------------------------------------------------------------------------
*/

Route::post('/tasks', [
    TaskController::class,
    'store'
])->name('tasks.store');



Route::post('/tasks/{id}/archive', [
    TaskController::class,
    'archive'
])->name('tasks.archive');



Route::get('/tasks', function () {

    return redirect()->route('dashboard');

});



Route::delete('/tasks/{id}', [
    TaskController::class,
    'destroy'
])->name('tasks.destroy');



/*
|--------------------------------------------------------------------------
| Gestion des projets
|--------------------------------------------------------------------------
*/

Route::delete('/projects/{project}', [
    ProjectController::class,
    'destroy'
])->name('projects.destroy');



Route::delete('/projects/{id}', [
    TaskController::class,
    'destroyProject'
])->name('projects.destroy');



/*
|--------------------------------------------------------------------------
| Gestion des catégories
|--------------------------------------------------------------------------
*/

Route::delete('/categories/{id}', [
    TaskController::class,
    'destroyCategory'
])->name('categories.destroy');





/*
|--------------------------------------------------------------------------
| Développement personnel - Livres PDF
|--------------------------------------------------------------------------
*/


// Protection accès direct GET
Route::get('/personal-resources', function () {

    return redirect()->route('dashboard', [
        'view' => 'personal'
    ]);

});



// Ajouter un livre PDF
Route::post('/personal-resources', [
    PersonalResourceController::class,
    'store'

])->name('personal-resources.store');



// Supprimer un livre
Route::delete('/personal-resources/{id}', [
    PersonalResourceController::class,
    'destroy'

])->name('personal-resources.destroy');



// Ouvrir un livre
Route::get('/personal-resources/{id}', [
    PersonalResourceController::class,
    'show'

])->name('personal-resources.show');



// Sauvegarder notes/statut
Route::put('/personal-resources/{id}', [
    PersonalResourceController::class,
    'update'

])->name('personal-resources.update');



// Sauvegarder progression lecture PDF
Route::post('/personal-resources/{id}/progress', [
    PersonalResourceController::class,
    'progress'

])->name('personal-resources.progress');





/*
|--------------------------------------------------------------------------
| TEST CONFIGURATION PHP RENDER
|--------------------------------------------------------------------------
|
| TEMPORAIRE
| À supprimer après vérification
|
*/

Route::get('/php-info-test', function () {


    return [

        'upload_max_filesize' => ini_get('upload_max_filesize'),

        'post_max_size' => ini_get('post_max_size'),

        'memory_limit' => ini_get('memory_limit'),

        'max_execution_time' => ini_get('max_execution_time'),

        'max_input_time' => ini_get('max_input_time'),

    ];


});