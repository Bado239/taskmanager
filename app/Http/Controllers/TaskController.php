<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use App\Models\Project;

class TaskController extends Controller
{
    /**
     * Affiche le Tableau de bord avec indicateurs globaux et séparation des vues
     */
    public function index(Request $request)
    {
        $view = $request->query('view', session('active_view', 'dashboard'));
        
        if (in_array($view, ['office', 'master'])) {
            session(['current_mode' => $view]);
        }
        
        session(['active_view' => $view]);
        $currentMode = session('current_mode', 'office');

        $categories = Category::all();
        $projects = Project::all();

        $totalTasks = Task::count();
        $todoTasks  = Task::where('document_status', 'todo')->count();
        $doingTasks = Task::where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where('document_status', 'done')->count();

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
     * Bascule le mode courant et redirige vers la vue correspondante
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
        $validator = \Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type'  => 'required|string|in:office,master',
            'new_project_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $targetView = in_array($request->type, ['office', 'master']) ? $request->type : 'dashboard';
            return redirect()->route('dashboard', ['view' => $targetView])
                ->withErrors($validator)
                ->withInput();
        }

        // Gestion de l'association ou de la création dynamique d'un projet
        $projectId = $request->project_id;

        if ($projectId === 'new' && $request->filled('new_project_name')) {
            $newProject = Project::create([
                'title' => $request->new_project_name,
            ]);
            $projectId = $newProject->id;
        } elseif (empty($projectId) || $projectId === 'new') {
            $projectId = null;
        }

        Task::create([
            'title'           => $request->title,
            'document_link'   => $request->document_link,
            'category_id'     => $request->category_id ?: null,
            'project_id'      => $projectId,
            'document_status' => $request->document_status ?? 'todo',
            'priority'        => $request->priority ?? 'medium',
            'date_prevue'     => $request->date_prevue,
            'type'            => $request->type,
        ]);

        $targetView = in_array($request->type, ['office', 'master']) ? $request->type : 'dashboard';

        return redirect()->route('dashboard', ['view' => $targetView])
            ->with('success', 'Livrable enregistré avec succès !');
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

    /**
     * Supprime un projet existant
     */
    public function destroyProject($id)
    {
        $project = Project::findOrFail($id);
        
        // Optionnel : Si vos tâches ont une contrainte en cascade, elles seront supprimées. 
        // Si vous voulez détacher les tâches avant de supprimer le projet :
        // $project->tasks()->update(['project_id' => null]);

        $project->delete();

        return redirect()->back()->with('success', 'Projet supprimé avec succès !');
    }
    
}