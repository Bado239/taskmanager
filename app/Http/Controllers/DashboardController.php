<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Category;
use App\Models\PersonalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Mode d'affichage actif : 'dashboard' (par défaut), 'office', 'master' ou 'personal'
        $view = $request->get('view', 'dashboard');

        // 📊 Indicateurs globaux (Calculés sur TOUTES les tâches du système)
        $totalTasks = Task::count();
        $todoTasks  = Task::where('document_status', 'todo')->count();
        $doingTasks = Task::where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where('document_status', 'done')->count();

        // Taux d'avancement global pour le Dashboard autonome
        $completionRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

        // 💼 Tâches filtrées spécifiquement si l'utilisateur bascule sur un mode
        $officeTasks = Task::with(['project', 'category'])
            ->where('type', 'office')
            ->latest()
            ->get();

        $masterTasks = Task::with(['project', 'category'])
            ->where('type', 'master')
            ->latest()
            ->get();

        // 📋 Liste complète des tâches pour la vue Dashboard indépendante
        $allTasks = Task::with(['project', 'category'])
            ->latest()
            ->get();

        // 🌱 Ressources personnelles pour le mode Développement Personnel
        $personalResources = collect();
        if ($view === 'personal') {
            $personalResources = PersonalResource::where('is_active', true)
                ->latest()
                ->get();
        }

        // 📅 Métriques complémentaires
        $tasksToday = Task::with(['project', 'category'])
            ->whereDate('date_prevue', $today)
            ->orderBy('ordre')
            ->orderBy('heure_debut')
            ->get();

        $urgentTasks = Task::where('priority', 'high')
            ->where('document_status', '!=', 'done')
            ->count();

        $tasksWeek = Task::whereBetween('date_prevue', [
            $today,
            $today->copy()->addDays(7)
        ])->count();

        $totalMinutesToday = Task::whereDate('date_prevue', $today)
            ->sum('duree_estimee');

        // Collections pour alimenter les formulaires
        $categories = Category::all();
        $projects   = Project::all();

        return view('dashboard', compact(
            'view',
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'doneTasks',
            'completionRate',
            'allTasks',
            'officeTasks',
            'masterTasks',
            'personalResources',
            'tasksToday',
            'urgentTasks',
            'tasksWeek',
            'totalMinutesToday',
            'categories',
            'projects'
        ));
    }
}