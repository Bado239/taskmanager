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
use App\Services\DocumentReaderService;
use App\Services\AICourseGeneratorService;
use App\Models\GeneratedCourse;
use App\Services\SupabaseStorageService;
use App\Services\ReadingProgressService;

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

        
        // RECHERCHE DES TÂCHES
        $search = trim((string) $request->query('search', ''));
        $search = mb_substr($search, 0, 100);

        // Afficher les résultats sur le Dashboard global
        if ($search !== '' && $view === 'dashboard' && !$indicator) {
            $indicator = 'total';
        }

        // Filtre de recherche réutilisable
        $applyTaskSearch = static function ($query) use ($search) {

            if ($search === '') {
                return;
            }

            $needle = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($needle) {

                $q->whereRaw('LOWER(title) LIKE ?', [$needle])

                  ->orWhereRaw(
                      'LOWER(project_name) LIKE ?',
                      [$needle]
                  )

                  ->orWhereHas('project', function ($p) use ($needle) {
                      $p->whereRaw(
                          'LOWER(title) LIKE ?',
                          [$needle]
                      );
                  })

                  ->orWhereHas('category', function ($c) use ($needle) {
                      $c->whereRaw(
                          'LOWER(name) LIKE ?',
                          [$needle]
                      );
                  });
            });
        };


        if (in_array($view, ['office', 'master', 'personal'])) {
            session(['current_mode' => $view]);
        }

        session(['active_view' => $view]);
        $currentMode = session('current_mode', 'office');

        $today = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i');


        // Listes des projets : Actifs OU encore rattachés à des tâches non archivées
        $projectsOffice = Project::where('type', 'office')->where(function($q) {
            $q->whereRaw('is_active = true')
              ->orWhereIn('id', Task::where('type', 'office')->where('is_archived', 0)->pluck('project_id'));
        })->get();


        $projectsMaster = Project::where('type', 'master')
            ->orderBy('created_at', 'desc')
            ->get();


        $categoriesOffice = Category::where('type', 'office')->get();
        $categoriesMaster = Category::where('type', 'master')->get();

        // Date du prochain examen pour chaque dossier Master.
        // Les examens passés ne sont pas prioritaires.
        $upcomingExamDates = \App\Models\Exam::query()
            ->whereDate('exam_date', '>=', Carbon::today())
            ->whereHas('project', function ($query) {
                $query->where('type', 'master');
            })
            ->selectRaw('project_id, MIN(exam_date) AS next_exam_date')
            ->groupBy('project_id')
            ->pluck('next_exam_date', 'project_id');

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
            $applyTaskSearch($query);

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
        $applyTaskSearch($officeQuery);

        $officeTasks = $officeQuery->with(['category', 'project'])
            ->orderBy('execution_date', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();


        // Charger les tâches Master.
        // Les filtres de la nouvelle navigation seront appliqués
        // dans dashboard.blade.php.

        $masterQuery = Task::where('type', 'master');

        $applyTaskSearch($masterQuery);

        $masterTasks = $masterQuery
            ->with(['category', 'project'])
            ->orderBy('execution_date', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

// 🌱 Gestion bibliothèque personnelle

        $personalResources = collect();

        $readingBooks = collect();

        $globalLibraryBooks = collect();

        $readingProgress = [];


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


            $service = new ReadingProgressService();


            foreach($readingBooks as $book)
            {

                $goal = \App\Models\ReadingGoal::where(
                    'personal_resource_id',
                    $book->id
                )
                ->first();


                if($goal)
                {

                    $readingProgress[$book->id] =
                        $service->calculate(
                            $book,
                            $goal
                        );

                }

            }



            // Bibliothèque globale : tous les livres
            $globalLibraryBooks = PersonalResource::whereRaw('is_active = true')
                ->where('type','book')
                ->latest()
                ->get();

        }
        return view('dashboard', compact(
            'view',
            'search',
            'filter',
            'statusFilter',
            'currentMode',
            'totalTasks',
            'todoTasks',
            'doingTasks',
            'readingProgress',
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
            'categoriesMaster',
            'upcomingExamDates'
        ));
    }


    /**
     * Modifier une tâche depuis l'emploi du temps global.
     */
    public function updateScheduleTask(Request $request, Task $task)
    {
        // La tâche doit appartenir à Office ou Master.
        if (!in_array($task->type, ['office', 'master'])) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',

            'project_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('projects', 'id')
                    ->where('type', $task->type)
                    ->where('is_active', true),
            ],

            'category_id' => [
                'nullable',
                'integer',
                \Illuminate\Validation\Rule::exists('categories', 'id')
                    ->where('type', $task->type),
            ],

            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',

            'document_status' => 'required|in:todo,in_progress,done',
            'priority' => 'required|in:low,medium,high',
        ]);

        $project = Project::findOrFail($data['project_id']);

        $task->update([
            'title' => $data['title'],
            'project_id' => $project->id,
            'project_name' => $project->title,
            'category_id' => $data['category_id'] ?? null,
            'heure_debut' => $data['start_time'],
            'heure_fin' => $data['end_time'],
            'document_status' => $data['document_status'],
            'priority' => $data['priority'],
        ]);

        return redirect()
            ->route('schedule.today')
            ->with('success', 'Tâche modifiée avec succès !');
    }


    private function nextScheduleSlot(): array
    {
        $start = Carbon::now('Africa/Dakar')->startOfMinute();

        for ($i = 0; $i < 365; $i++) {

            $date = $start->toDateString();

            // Dernière heure de fin des tâches programmées
            // pour cette date, Office et Master confondus.
            $lastTask = Task::query()
                ->whereIn('type', ['office', 'master'])
                ->where('is_archived', 0)
                ->whereDate('execution_date', $date)
                ->whereNotNull('heure_fin')
                ->orderBy('heure_fin', 'desc')
                ->first();

            if ($lastTask) {

                $lastEnd = Carbon::parse(
                    $date . ' ' . $lastTask->heure_fin,
                    'Africa/Dakar'
                );

                // Ne jamais proposer une heure déjà passée.
                if ($lastEnd->gt($start)) {
                    $start = $lastEnd;
                }
            }

            $end = $start->copy()->addHours(2);

            // Conserver des créneaux de 2 heures
            // entièrement compris dans la même journée.
            if ($end->toDateString() === $start->toDateString()) {
                return [
                    'date' => $start->toDateString(),
                    'start' => $start->format('H:i'),
                    'end' => $end->format('H:i'),
                ];
            }

            // Sinon, passer au lendemain à 09:00.
            $start = $start->copy()
                ->addDay()
                ->startOfDay()
                ->setTime(9, 0);
        }

        throw new \RuntimeException(
            'Aucun créneau disponible dans les 365 prochains jours.'
        );
    }

    
/**
 * Emploi du temps global du jour.
 * Regroupe les tâches Office et Master.
 */
    public function todaySchedule()
    {

        $today = Carbon::today('Africa/Dakar');

        // Tâches des jours précédents non terminées.
        $overdueTasks = Task::query()
            ->whereIn('type', ['office', 'master'])
            ->where('is_archived', 0)
            ->where('document_status', '!=', 'done')
            ->whereDate('execution_date', '<', $today->toDateString())
            ->orderBy('execution_date')
            ->orderBy('heure_debut')
            ->get();

        // Reporter chaque tâche au prochain créneau disponible.
        foreach ($overdueTasks as $overdueTask) {

            $slot = $this->nextScheduleSlot();

            $overdueTask->update([
                'execution_date' => $slot['date'],
                'heure_debut' => $slot['start'],
                'heure_fin' => $slot['end'],
            ]);
        }

        // Recharger ensuite le programme du jour.
        $todayTasks = Task::with(['project', 'category'])
            ->whereIn('type', ['office', 'master'])
            ->whereDate('execution_date', $today->toDateString())
            ->where('is_archived', 0)
            ->get()
            ->sortBy(function ($task) {

                // 0 : tâches non terminées
                // 1 : tâches terminées
                $group = $task->document_status === 'done' ? '1' : '0';

                // Les tâches sans horaire viennent après.
                $time = $task->heure_debut
                    ? substr((string) $task->heure_debut, 0, 5)
                    : '99:99';

                return $group . '|' . $time . '|' .
                    str_pad((string) $task->id, 12, '0', STR_PAD_LEFT);
            })
            ->values();


        $officeCount = $todayTasks->where('type', 'office')->count();
        $masterCount = $todayTasks->where('type', 'master')->count();

        $completedCount = $todayTasks
            ->where('document_status', 'done')
            ->count();

        // Projets et matières actifs des deux modes.
        $scheduleProjects = Project::whereIn('type', ['office', 'master'])
            ->whereRaw('is_active = TRUE')
            ->orderBy('title')
            ->get();

        // Étapes et leçons des deux modes.
        $scheduleCategories = Category::whereIn('type', ['office', 'master'])
            ->orderBy('name')
            ->get();


        $nextSlot = $this->nextScheduleSlot();

        return view('schedule.today', compact(
            'today',
            'todayTasks',
            'officeCount',
            'masterCount',
            'completedCount',
            'scheduleProjects',
            'scheduleCategories',
            'nextSlot'
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
            'document_link' => 'nullable|url:http,https|max:2048',
            'document_file' => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png',
            'execution_date' => 'required_if:return_to,schedule.today|nullable|date',
            'date_prevue' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);


        if ($request->input('return_to') === 'schedule.today') {

            $validator->after(function ($validator) use ($request) {

                $validProject = Project::where('id', $request->project_id)
                    ->where('type', $request->type)
                    ->whereRaw('is_active = TRUE')
                    ->exists();

                if (!$validProject) {
                    $validator->errors()->add(
                        'project_id',
                        'Sélectionnez un projet ou une matière valide.'
                    );
                }

                if ($request->filled('category_id')) {
                    $validCategory = Category::where('id', $request->category_id)
                        ->where('type', $request->type)
                        ->exists();

                    if (!$validCategory) {
                        $validator->errors()->add(
                            'category_id',
                            'La leçon ne correspond pas au mode sélectionné.'
                        );
                    }
                }

                if (
                    !$request->filled('execution_date') ||
                    !\Carbon\Carbon::parse($request->execution_date)
                        ->startOfDay()
                        ->gte(\Carbon\Carbon::today('Africa/Dakar'))
                ) {
                    $validator->errors()->add(
                        'execution_date',
                        'La date de programmation ne peut pas être passée.'
                    );
                }

                if (
                    !$request->filled('start_time') ||
                    !$request->filled('end_time') ||
                    $request->end_time <= $request->start_time
                ) {
                    $validator->errors()->add(
                        'end_time',
                        'L’heure de fin doit être postérieure à l’heure de début.'
                    );
                }

            });
        }



        if ($validator->fails()) {

            if ($request->input('return_to') === 'schedule.today') {
                return redirect()
                    ->route('schedule.today')
                    ->withErrors($validator)
                    ->withInput();
            }

            $targetView = in_array($request->type, ['office', 'master'])
                ? $request->type
                : 'dashboard';

            return redirect()
                ->route('dashboard', ['view' => $targetView])
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


        // Gestion du lien Internet ou du fichier joint
        $documentLink = $request->filled('document_link')
            ? $request->input('document_link')
            : null;

        // Si un fichier est envoyé, il est prioritaire sur l'URL.
        if ($request->hasFile('document_file')) {

            $storage = new SupabaseStorageService();

            $documentLink = $storage->upload(
                $request->file('document_file')
            );
        }

        // Enregistrement de la tâche
        Task::create([
            'title'           => $request->title,
            'document_link'   => $documentLink,
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



        // Une tâche créée depuis l'emploi du temps
        // reste sur la page de l'emploi du temps.
        if ($request->input('return_to') === 'schedule.today') {
            return redirect()
                ->route('schedule.today')
                ->with('success', 'Tâche ajoutée à votre emploi du temps !');
        }

        $targetView = in_array($request->type, ['office', 'master'])
            ? $request->type
            : 'dashboard';

        $redirectParams = ['view' => $targetView];

        // Revenir dans le dossier après la création.
        if ($targetView === 'master' && $projectId && $projectId !== 'new') {
            $redirectParams['folder'] = $projectId;
        }

        return redirect()
            ->route('dashboard', $redirectParams)
            ->with('success', 'Tâche enregistrée avec succès !');
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
        $task = Task::with([
            'project',
            'category'
        ])->findOrFail($id);


        // Construction de la recherche
        $subject = trim(
            ($task->project->title ?? $task->project_name ?? '')
            .' '
            .($task->category->name ?? '')
            .' '
            .($task->title ?? '')
        );


        // Recherche des vrais cours
        $resources = $service->search($subject);



        // Supprimer les anciens résultats
        CourseResource::where('task_id', $task->id)->delete();



        // Enregistrer uniquement les vrais résultats
        if(is_array($resources))
        {

            foreach($resources as $resource)
            {

                CourseResource::create([

                    'task_id' => $task->id,

                    'title' => $resource['title'] ?? 'Cours sans titre',

                    'source' => $resource['source'] ?? 'Web',

                    'url' => $resource['url'] ?? null,

                    'type' => $resource['type'] ?? 'Cours Web',

                    'file_type' => $resource['file_type'] ?? 'WEB',

                    'is_university' => $resource['is_university'] ?? false,

                    'score' => $resource['score'] ?? 0,

                ]);

            }

        }



        return redirect()
            ->route('tasks.learning',$task->id)
            ->with(
                'success',
                'Cours actualisés avec succès'
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

            'learningDocuments',

            'generatedCourse'

        ])->findOrFail($id);


        return view(
            'tasks.learning',
            compact('task')
        );
    }

    public function edit($id)
    {
        $task = Task::with(['project','category'])->findOrFail($id);

        $projects = Project::where('type',$task->type)->get();
        $categories = Category::where('type',$task->type)->get();

        return view('tasks.edit', compact(
            'task',
            'projects',
            'categories'
        ));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'document_link' => 'nullable|url:http,https|max:2048',
            'document_file' => 'nullable|file|max:10240',
            'date_prevue' => 'nullable|date',
            'execution_date' => 'nullable|date',
        ]);


        $data = [

            'title' => $request->title,

            'project_id' => $request->project_id,

            'project_name' => optional(Project::find($request->project_id))->title,

            'category_id' => $request->category_id,

            'document_status' => $request->document_status,

            'priority' => $request->priority,

            'date_prevue' => $request->date_prevue,

            'execution_date' => $request->execution_date,

            'heure_debut' => $request->start_time,

            'heure_fin' => $request->end_time,

            'type' => $task->type,

        ];



        // ==========================
        // GESTION DU LIEN OU DU FICHIER
        // ==========================

        // Un lien renseigné remplace l'ancien document.
        if ($request->filled('document_link')) {
            $data['document_link'] = $request->input('document_link');
        }

        // Un nouveau fichier reste prioritaire.
        if($request->hasFile('document_file')){


            $storage = new SupabaseStorageService();


            $documentLink = $storage->upload(
                $request->file('document_file')
            );


            $data['document_link'] = $documentLink;


        }

        $task->update($data);




        $redirectParams = ['view' => $task->type];

        if ($task->type === 'master' && $task->project_id) {
            $redirectParams['folder'] = $task->project_id;
        }

        return redirect()
            ->route('dashboard', $redirectParams)
            ->with('success', 'Tâche modifiée avec succès !');
    }

    public function editData($id)
    {
        $task = Task::findOrFail($id);

        return response()->json([
            'id'=>$task->id,
            'title'=>$task->title,
            'project_id'=>$task->project_id,
            'category_id'=>$task->category_id,
            'document_link'=>$task->document_link,
            'date_prevue'=>$task->date_prevue,
            'execution_date'=>$task->execution_date,
            'heure_debut'=>$task->heure_debut,
            'heure_fin'=>$task->heure_fin,
            'document_status'=>$task->document_status,
            'priority'=>$task->priority,
        ]);
    }

}
