<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Affiche le Tableau de bord avec indicateurs globaux, filtres et séparation des vues
     */
    public function index(Request $request)
    {
        $view = $request->query('view', session('active_view', 'dashboard'));
        $filter = $request->query('filter', 'all'); // 'all' ou 'today'
        $statusFilter = $request->query('status', 'active'); // 'active' ou 'archived'

        if (in_array($view, ['office', 'master'])) {
            session(['current_mode' => $view]);
        }
        
        session(['active_view' => $view]);
        $currentMode = session('current_mode', 'office');

        $today = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i');

        // Automatisation de l'archivage avec DB::raw pour forcer le type booléen sous PostgreSQL
        $tasksToCheck = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->get();
        foreach ($tasksToCheck as $task) {
            $isFinishedToday = ($task->execution_date === $today && $task->heure_fin && $task->heure_fin <= $currentTime);
            $isValidatedPassed = ($task->document_status === 'done' && $task->execution_date && $task->execution_date < $today);

            if ($isFinishedToday || $isValidatedPassed) {
                $task->update(['is_archived' => true]);
            }
        }

        $categories = Category::all();
        $projects = Project::all();

        // Indicateurs globaux (Tâches actives non archivées)
        $totalTasks = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->count();
        $todoTasks  = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->where('document_status', 'todo')->count();
        $doingTasks = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->where('document_status', 'done')->count();

        // Nouveaux indicateurs demandés
        $officeTodayCount = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->where('type', 'office')->where('execution_date', $today)->count();
        $masterTodayCount = Task::where(DB::raw('is_archived'), '=', DB::raw('false'))->where('type', 'master')->where('execution_date', $today)->count();
        $archivedCount = Task::where(DB::raw('is_archived'), '=', DB::raw('true'))->count();

        // Requête Mode Office avec filtres
        $officeQuery = Task::where('type', 'office');
        if ($statusFilter === 'archived') {
            $officeQuery->where(DB::raw('is_archived'), '=', DB::raw('true'));
        } else {
            $officeQuery->where(DB::raw('is_archived'), '=', DB::raw('false'));
            if ($filter === 'today') {
                $officeQuery->where('execution_date', $today);
            }
        }
        $officeTasks = $officeQuery->with(['category', 'project'])->latest()->get();

        // Requête Mode Master avec filtres
        $masterQuery = Task::where('type', 'master');
        if ($statusFilter === 'archived') {
            $masterQuery->where(DB::raw('is_archived'), '=', DB::raw('true'));
        } else {
            $masterQuery->where(DB::raw('is_archived'), '=', DB::raw('false'));
            if ($filter === 'today') {
                $masterQuery->where('execution_date', $today);
            }
        }
        $masterTasks = $masterQuery->with(['category', 'project'])->latest()->get();

        return view('dashboard', compact(
            'view',
            'filter',
            'statusFilter',
            'currentMode',
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'doneTasks',
            'officeTodayCount',
            'masterTodayCount',
            'archivedCount',
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
            'new_category_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $targetView = in_array($request->type, ['office', 'master']) ? $request->type : 'dashboard';
            return redirect()->route('dashboard', ['view' => $targetView])
                ->withErrors($validator)
                ->withInput();
        }

        // Gestion du projet
        $projectId = $request->project_id;
        if ($projectId === 'new' && $request->filled('new_project_name')) {
            $newProject = Project::create([
                'title' => $request->new_project_name,
            ]);
            $projectId = $newProject->id;
        } elseif (empty($projectId) || $projectId === 'new') {
            $projectId = null;
        }

        // Gestion de l'étape (catégorie)
        $categoryId = $request->category_id;
        if ($categoryId === 'new' && $request->filled('new_category_name')) {
            $newCategoryName = $request->new_category_name;
            $newCategory = Category::create([
                'name'  => $newCategoryName,
                'title' => $newCategoryName,
            ]);
            $categoryId = $newCategory->id;
        } else {
            if (empty($categoryId) || $categoryId === 'new') {
                $defaultCategory = Category::first();
                $categoryId = $defaultCategory ? $defaultCategory->id : null;
            }
        }

        // Enregistrement
        Task::create([
            'title'           => $request->title,
            'document_link'   => $request->document_link,
            'category_id'     => $categoryId,
            'project_id'      => $projectId,
            'document_status' => $request->document_status ?? 'todo',
            'priority'        => $request->priority ?? 'medium',
            'date_prevue'     => $request->date_prevue,
            'execution_date'  => $request->execution_date,
            'heure_debut'     => $request->start_time,
            'heure_fin'       => $request->end_time,
            'type'            => $request->type,
            'is_archived'     => false,
        ]);

        $targetView = in_array($request->type, ['office', 'master']) ? $request->type : 'dashboard';

        return redirect()->route('dashboard', ['view' => $targetView])
            ->with('success', 'Livrable enregistré avec succès !');
    }

    /**
     * Archive manuellement une tâche
     */
    public function archive($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['is_archived' => true]);

        return redirect()->back()->with('success', 'Tâche archivée avec succès !');
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
        $project->delete();

        return redirect()->back()->with('success', 'Projet supprimé avec succès !');
    }

    /**
     * Supprime une étape / catégorie existante
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Étape supprimée avec succès !');
    }
}