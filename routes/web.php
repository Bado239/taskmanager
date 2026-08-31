<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PersonalResourceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CourseResourceController;
use App\Http\Controllers\LearningDocumentController;


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


// Ajouter un livre à la bibliothèque à lire
Route::post('/personal-resources/{id}/add-reading', [
    PersonalResourceController::class,
    'addReading'

])->name('personal-resources.add-reading');


// Sauvegarder progression lecture PDF
Route::post('/personal-resources/{id}/progress', [
    PersonalResourceController::class,
    'progress'

])->name('personal-resources.progress');

// Emploi du temps Master

Route::get(
    '/schedule',
    [ScheduleController::class,'index']
)
->name('schedule.index');


Route::post(
    '/schedule',
    [ScheduleController::class,'store']
)
->name('schedule.store');


Route::get('/tasks/{task}/learning',
    [TaskController::class, 'learning']
)->name('tasks.learning');

Route::get(
    '/tasks/{task}/search-courses',
    [TaskController::class,'searchCourses']
)
->name('tasks.searchCourses');

/*
|--------------------------------------------------------------------------
| Evaluation des ressources de cours
|--------------------------------------------------------------------------
*/


Route::post(
    '/course-resources/{id}/relevance',
    [CourseResourceController::class,'relevance']
)
->name('courses.relevance');

Route::post(
    '/course-resource/{id}/rate',
    [CourseResourceController::class,'rate']
)->name('course.rate');


Route::post(
    '/course-resource/{id}/save',
    [CourseResourceController::class,'save']
)->name('course.save');


Route::post(
    '/course-resource/{id}/note',
    [CourseResourceController::class,'note']
)->name('course.note');



Route::post(
    '/learning-documents',
    [LearningDocumentController::class,'store']
)->name('learning-documents.store');