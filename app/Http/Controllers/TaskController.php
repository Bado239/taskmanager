<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use App\Models\Project;

class TaskController extends Controller
{
    /**
     * Affiche le Tableau de bord avec l'analyse des KPI
     */
    public function index(Request $request)
    {
        $currentMode = session('current_mode', 'office');

        // Récupération des catégories et projets
        $categories = Category::all();
        $projects = Project::all();

        // Calcul des métriques KPI selon le mode actif (office / master)
        $totalTasks = Task::where('type', $currentMode)->count();
        $todoTasks  = Task::where('type', $currentMode)->where('document_status', 'todo')->count();
        $doingTasks = Task::where('type', $currentMode)->where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where('type', $currentMode)->where('document_status', 'done')->count();

        // Tâches du jour pour la liste
        $todayTasks = Task::where('type', $currentMode)
                          ->with(['category', 'project'])
                          ->latest()
                          ->get();

        return view('dashboard', compact(
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'doneTasks',
            'todayTasks',
            'categories',
            'projects',
            'currentMode'
        ));
    }

    /**
     * Bascule le mode entre Office et Master
     */
    public function switchMode($mode)
    {
        if (in_array($mode, ['office', 'master'])) {
            session(['current_mode' => $mode]);
        }

        return redirect()->back();
    }

    /**
     * Enregistre une nouvelle tâche / livrable
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type'  => 'required|string',
        ]);

        // Gestion de la création rapide d'un projet si sélectionné
        $projectId = $request->project_id;
        if ($request->project_id === 'new' && $request->filled('new_project_name')) {
            $newProject = Project::create([
                'name' => $request->new_project_name
            ]);
            $projectId = $newProject->id;
        }

        Task::create([
            'title'           => $request->title,
            'document_link'   => $request->document_link,
            'category_id'     => $request->category_id,
            'project_id'      => $projectId,
            'document_status' => $request->document_status ?? 'todo',
            'priority'        => $request->priority ?? 'medium',
            'due_date'        => $request->date_prevue,
            'type'            => $request->type ?? 'office',
        ]);

        return redirect()->back()->with('success', 'Livrable enregistré avec succès !');
    }

    /**
     * Supprime une tâche
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->back()->with('success', 'Tâche supprimée avec succès !');
    }
}