<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use App\Models\Project;

class TaskController extends Controller
{
    /**
     * Affiche le Tableau de bord avec séparation Dashboard / Office / Master
     */
    public function index(Request $request)
    {
        // Récupère la vue demandée (dashboard par défaut, office, ou master)
        $view = $request->query('view', session('active_view', 'dashboard'));
        
        // Si la vue est office ou master, on met à jour le mode courant en session
        if (in_array($view, ['office', 'master'])) {
            session(['current_mode' => $view]);
        }
        
        session(['active_view' => $view]);
        $currentMode = session('current_mode', 'office');

        // Récupération des catégories et projets
        $categories = Category::all();
        $projects = Project::all();

        // KPIs pour le Dashboard (mode courant)
        $totalTasks = Task::where('type', $currentMode)->count();
        $todoTasks  = Task::where('type', $currentMode)->where('document_status', 'todo')->count();
        $doingTasks = Task::where('type', $currentMode)->where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where('type', $currentMode)->where('document_status', 'done')->count();

        // Tâches Office et Master
        $officeTasks = Task::where('type', 'office')
                           ->with(['category', 'project'])
                           ->latest()
                           ->get();

        $masterTasks = Task::where('type', 'master')
                           ->with(['category', 'project'])
                           ->latest()
                           ->get();

        return view('dashboard', compact(
            'view',
            'currentMode',
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'doneTasks',
            'officeTasks',
            'masterTasks',
            'categories',
            'projects'
        ));
    }

    /**
     * Bascule le mode courant
     */
    public function switchMode(Request $request, $mode)
    {
        if (in_array($mode, ['office', 'master'])) {
            session(['current_mode' => $mode, 'active_view' => $mode]);
        }

        return redirect()->route('dashboard', ['view' => $mode]);
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

        $projectId = $request->project_id;
        if ($request->project_id === 'new' && $request->filled('new_project_name')) {
            $newProject = Project::create([
                'title' => $request->new_project_name,
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
            'type'            => $request->type ?? session('current_mode', 'office'),
        ]);

        return redirect()->route('dashboard', ['view' => $request->type])->with('success', 'Livrable enregistré avec succès !');
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