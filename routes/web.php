<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Apport crucial pour gérer les transactions
use App\Models\Schedule;
use Illuminate\Http\Request;

// Page d'accueil : redirige automatiquement vers les tâches du jour
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// 📊 Tableau de bord avec indicateurs cliquables
Route::get('/tasks/dashboard', [TaskController::class, 'dashboard'])->name('tasks.dashboard');

// 📋 CRUD des tâches (index, create, store, edit, update, destroy)
Route::resource('tasks', TaskController::class);

// 📰 Page de Veille Technologique
Route::get('/veille-tech', [TaskController::class, 'veilleTech'])->name('tasks.veille');


// 🧰 Outils de Maintenance / Réinitialisation
Route::get('/force-clean-db', function () {
    try {
        // 1. On annule toutes les transactions SQL bloquées
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // 2. Désactiver temporairement les contraintes de clé étrangère
        Schema::disableForeignKeyConstraints();

        // 3. Force la réinitialisation complète des tables
        Artisan::call('migrate:fresh', ['--force' => true]);

        // 4. Force l'exécution du DatabaseSeeder avec les nouvelles modalités
        Artisan::call('db:seed', ['--force' => true]);

        // 5. Réactiver les contraintes de clé étrangère
        Schema::enableForeignKeyConstraints();

        return "Base de données réinitialisée et repeuplée avec succès !";
    } catch (\Exception $e) {
        Schema::enableForeignKeyConstraints();
        return "Erreur lors de la réinitialisation : " . $e->getMessage();
    }
});

// 🚀 ROUTE DE MIGRATION DE BASE DE DONNÉES (Sécurisée contre les blocages)
Route::get('/run-migration-bado', function () {
    try {
        // 1. On nettoie les transactions PostgreSQL fantômes avant de lancer les migrations
        while (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        Schema::disableForeignKeyConstraints();

        // Exécute les nouvelles migrations
        Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = Artisan::output();

        Schema::enableForeignKeyConstraints();

        return "<h3>Migration réussie !</h3>
                <p>Résultat des migrations :</p>
                <pre>" . $migrationOutput . "</pre>";
    } catch (\Exception $e) {
        Schema::enableForeignKeyConstraints();
        return "Erreur lors de la maintenance : " . $e->getMessage();
    }
});


// 🖼️ Enregistrement du lien de l'emploi du temps (Compatible Render Gratuit ☁️)
Route::post('/schedule-upload', function (Request $request) {
    // On valide que l'entrée est bien une URL valide
    $request->validate([
        'schedule_url' => 'required|url',
    ]);

    // On enregistre simplement l'adresse URL externe de l'image
    Schedule::create([
        'file_path' => $request->schedule_url
    ]);

    return back()->with('success', 'Emploi du temps mis à jour avec succès !');
})->name('schedule.upload');

// Bascule entre Mode Bureau et Mode Master
Route::get('/switch-mode/{mode}', [TaskController::class, 'switchMode'])->name('mode.switch');

// Sauvegarde des étapes de révision d'un cours
Route::post('/tasks/{task}/update-exam-prep', [TaskController::class, 'updateExamPrep'])->name('tasks.updatePrep');