<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 📅 Tâches du jour (planning réel)
        $tasksToday = Task::with(['project', 'category'])
            ->whereDate('date_prevue', $today)
            ->orderBy('ordre')
            ->orderBy('heure_debut')
            ->get();

        // 🔥 Tâches urgentes (non terminées)
        $urgentTasks = Task::where('priority', 'high')
            ->where('status', '!=', 'done')
            ->count();

        // 📆 Semaine en cours
        $tasksWeek = Task::whereBetween('date_prevue', [
                $today,
                $today->copy()->addDays(7)
            ])->count();

        // ⏱ Temps total estimé aujourd’hui
        $totalMinutesToday = Task::whereDate('date_prevue', $today)
            ->sum('duree_estimee');

        // 📊 Statistiques globales
        $totalTasks = Task::count();
        $todoTasks = Task::where('status', 'todo')->count();
        $doingTasks = Task::where('status', 'doing')->count();
        $doneTasks = Task::where('status', 'done')->count();

        $totalProjects = Project::count();
        $totalCategories = Category::count();

        return view('dashboard', compact(
            'tasksToday',
            'urgentTasks',
            'tasksWeek',
            'totalMinutesToday',
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'doneTasks',
            'totalProjects',
            'totalCategories'
        ));
    }
}