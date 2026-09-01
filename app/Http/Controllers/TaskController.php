<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Category;
use App\Models\Project;
use App\Models\PersonalResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\CourseSearchService;
use App\Models\CourseResource;

class TaskController extends Controller
{
    /**
     * Affiche le Tableau de bord avec indicateurs globaux, filtres et séparation des vues
     */
    public function index(Request $request)
    {
        $view = $request->query('view', session('active_view', 'dashboard'));
        $filter = $request->query('filter', 'all');
        $statusFilter = $request->query('status', 'active');
        $indicator = $request->query('indicator');

        if (in_array($view, ['office', 'master', 'personal'])) {
            session(['current_mode' => $view]);
        }

        session(['active_view' => $view]);
        $currentMode = session('current_mode', 'office');

        $today = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i');

        // Automatisation de l'archivage
        $tasksToCheck = Task::where('is_archived', 0)->get();
        foreach ($tasksToCheck as $task) {
            $isFinishedToday = ($task->execution_date === $today && $task->heure_fin && $task->heure_fin <= $currentTime);
            $isValidatedPassed = ($task->document_status === 'done' && $task->execution_date && $task->execution_date < $today);

            if ($isFinishedToday || $isValidatedPassed) {
                $task->update(['is_archived' => 1]);
            }
        }

        // Listes des projets : Actifs OU encore rattachés à des tâches non archivées
        $projectsOffice = Project::where('type', 'office')->where(function($q) {
            $q->whereRaw('is_active = true')
              ->orWhereIn('id', Task::where('type', 'office')->where('is_archived', 0)->pluck('project_id'));
        })->get();

        $projectsMaster = Project::where('type', 'master')->where(function($q) {
            $q->whereRaw('is_active = true')
              ->orWhereIn('id', Task::where('type', 'master')->where('is_archived', 0)->pluck('project_id'));
        })->get();

        $categoriesOffice = Category::where('type', 'office')->get();
        $categoriesMaster = Category::where('type', 'master')->get();

        if ($view === 'office') {
            $categories = $categoriesOffice;
            $projects = $projectsOffice;
        } elseif ($view === 'master') {
            $categories = $categoriesMaster;
            $projects = $projectsMaster;
        } else {
            $categories = Category::all();
            $projects = Project::where(function($q) {
                $q->whereRaw('is_active = true')
                  ->orWhereIn('id', Task::where('is_archived', 0)->pluck('project_id'));
            })->get();
        }

        // Indicateurs globaux
        $totalTasks = Task::where('is_archived', 0)->count();
        $todoTasks  = Task::where('is_archived', 0)->where('document_status', 'todo')->count();
        $doingTasks = Task::where('is_archived', 0)->where('document_status', 'in_progress')->count();
        $doneTasks  = Task::where('is_archived', 0)->where('document_status', 'done')->count();

        $officeTodayCount = Task::where('is_archived', 0)->where('type', 'office')->where('execution_date', $today)->count();
        $masterTodayCount = Task::where('is_archived', 0)->where('type', 'master')->where('execution_date', $today)->count();
        $archivedCount = Task::where('is_archived', 1)->count();

        // Si l'utilisateur a cliqué sur un indicateur du Dashboard Global
        $globalIndicatorTasks = collect();
        if ($view === 'dashboard' && $indicator) {
            $query = Task::query();
            switch ($indicator) {
                case 'total':
                    $query->where('is_archived', 0);
                    break;
                case 'todo':
                    $query->where('is_archived', 0)->where('document_status', 'todo');
                    break;
                case 'doing':
                    $query->where('is_archived', 0)->where('document_status', 'in_progress');
                    break;
                case 'done':
                    $query->where('is_archived', 0)->where('document_status', 'done');
                    break;
                case 'office_today':
                    $query->where('is_archived', 0)->where('type', 'office')->where('execution_date', $today);
                    break;
                case 'master_today':
                    $query->where('is_archived', 0)->where('type', 'master')->where('execution_date', $today);
                    break;
                case 'archived':
                    $query->where('is_archived', 1);
                    break;
            }
            $globalIndicatorTasks = $query->with(['category', 'project'])
                ->orderBy('execution_date', 'asc')
                ->orderBy('heure_debut', 'asc')
                ->get();
        }

        // Requête Mode Office avec filtres et tri chronologique
        $officeQuery = Task::where('type', 'office');
        if ($statusFilter === 'archived') {
            $officeQuery->where('is_archived', 1);
        } else {
            $officeQuery->where('is_archived', 0);
            if ($filter === 'today') {
                $officeQuery->where('execution_date', $today);
            }
        }
        $officeTasks = $officeQuery->with(['category', 'project'])
            ->orderBy('execution_date', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

        // Requête Mode Master avec filtres et tri chronologique
        $masterQuery = Task::where('type', 'master');
        if ($statusFilter === 'archived') {
            $masterQuery->where('is_archived', 1);
        } else {
            $masterQuery->where('is_archived', 0);
            if ($filter === 'today') {
                $masterQuery->where('execution_date', $today);
            }
        }
        $masterTasks = $masterQuery->with(['category', 'project'])
            ->orderBy('execution_date', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

// 🌱 Gestion bibliothèque personnelle

        $personalResources = collect();

        $readingBooks = collect();

        $globalLibraryBooks = collect();


        if ($view === 'personal') {


            // Tous les livres actifs
            $personalResources = PersonalResource::whereRaw('is_active = true')
                ->where('type','book')
                ->latest()
                ->get();



            // Bibliothèque à lire
            $readingBooks = PersonalResource::whereRaw('is_active = true')
                ->where('type','book')
                ->where('reading_status','reading')
                ->latest()
                ->get();



            // Bibliothèque globale : tous les livres
            $globalLibraryBooks = PersonalResource::whereRaw('is_active = true')
                ->where('type','book')
                ->latest()
                ->get();

        }
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
            'globalIndicatorTasks',
            'indicator',
            'officeTasks',
            'masterTasks',
            'personalResources',
            'readingBooks',
            'globalLibraryBooks',
            'categories',
            'projects',
            'projectsOffice',
            'projectsMaster',
            'categoriesOffice',
            'categoriesMaster'
        ));
    }

    /**
     * Bascule le mode courant et redirige vers la vue correspondante
     */
    public function switchMode(Request $request, $mode)
    {
        if (in_array($mode, ['office', 'master', 'personal'])) {
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

        // Gestion du projet avec association stricte du type
        $projectId = $request->project_id;
        $projectName = null;

        if ($projectId === 'new' && $request->filled('new_project_name')) {
            $newProject = Project::create([
                'title' => $request->new_project_name,
                'type'  => $request->type,
            ]);
            $projectId = $newProject->id;
            $projectName = $newProject->title;
        } elseif (!empty($projectId) && $projectId !== 'new') {
            $projectName = Project::find($projectId)?->title;
        } else {
            $projectId = null;
        }

        // Gestion de l'étape (catégorie) avec association stricte du type
        $categoryId = $request->category_id;

        if ($categoryId === 'new' && $request->filled('new_category_name')) {
            $newCategory = Category::create([
                'name' => $request->new_category_name,
                'type' => $request->type,
            ]);
            $categoryId = $newCategory->id;
        } elseif (!empty($categoryId) && $categoryId !== 'new') {
            $categoryId = Category::find($categoryId)?->id;
        } else {
            $categoryId = null;
        }

        // Enregistrement de la tâche
        Task::create([
            'title'           => $request->title,
            'document_link'   => $request->document_link,
            'category_id'     => $categoryId,
            'project_id'      => $projectId,
            'project_name'    => $projectName,
            'document_status' => $request->document_status ?? 'todo',
            'priority'        => $request->priority ?? 'medium',
            'date_prevue'     => $request->date_prevue,
            'execution_date'  => $request->execution_date,
            'heure_debut'     => $request->start_time,
            'heure_fin'       => $request->end_time,
            'type'            => $request->type,
            'is_archived'     => 0,
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
        $task->update(['is_archived' => 1]);

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
     * Désactive logiquement un projet
     */
    public function destroyProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $project->update([
            'is_active' => false
        ]);

        $message = 'Projet archivé avec succès. Les tâches conservent le nom du projet.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Supprime une étape / catégorie existante
     */
    public function destroyCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        Task::where('category_id', $id)->update(['category_id' => null]);

        $category->delete();

        $message = 'Étape supprimée avec succès (les tâches ont été conservées).';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function searchCourses(
        $id,
        \App\Services\CourseSearchService $service
    )
    {

        $task = Task::with('category')->findOrFail($id);


        $subject = trim(
            ($task->project_name ?? '')
            .' '
            .($task->category->name ?? '')
            .' '
            .($task->title ?? '')
        );

        $resources = $service->search($subject);



        // supprimer anciens résultats
        \App\Models\CourseResource::where(
            'task_id',
            $task->id
        )->delete();



        foreach($resources as $resource)
        {

            \App\Models\CourseResource::create([

                'task_id'=>$task->id,

                'title'=>$resource['title'],

                'source'=>$resource['source'],

                'url'=>$resource['url'],

                'type'=>$resource['type'],

                'file_type'=>$resource['file_type'],

                'is_university'=>
                    $resource['is_university']
                    ? 'true'
                    : 'false',

                'score'=>$resource['score'],

            ]);

        }



        return redirect()
            ->route(
                'tasks.learning',
                $task->id
            );

    }


    /**
     * Affiche l'espace apprentissage d'un chapitre
     */
    public function learning($id)
    {
        $task = Task::with([
            'project',
            'category',
            'courseResources' => function($query){
                $query->orderBy('score','desc')
                    ->limit(5);
            },
            'learningDocuments'
        ])->findOrFail($id);


        return view(
            'tasks.learning',
            compact('task')
        );
    }

}
