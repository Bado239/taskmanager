<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Mode d'affichage actif : 'dashboard', 'office' ou 'master'
        $view = $request->get('view', 'dashboard');

        // 📊 Indicateurs globaux (cumul Office + Master)
        $totalTasks = Task::count();
        $todoTasks  = Task::where('document_status', 'todo')->count();
        $doingTasks = Task::where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where('document_status', 'done')->count();

        // 💼 Tâches pour la vue Office
        $officeTasks = Task::with(['project', 'category'])
            ->where('type', 'office')
            ->latest()
            ->get();

        // 🎓 Tâches pour la vue Master
        $masterTasks = Task::with(['project', 'category'])
            ->where('type', 'master')
            ->latest()
            ->get();

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

        // Collections pour alimenter le formulaire d'ajout
        $categories = Category::all();
        $projects   = Project::all();

        return view('dashboard', compact(
            'view',
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'doneTasks',
            'officeTasks',
            'masterTasks',
            'tasksToday',
            'urgentTasks',
            'tasksWeek',
            'totalMinutesToday',
            'categories',
            'projects'
        ));
    }
}