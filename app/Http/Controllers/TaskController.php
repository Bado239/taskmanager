<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectStep;
use App\Models\Schedule;
use App\Models\ExamPrep; // Ajout du modèle d'examen
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Affiche uniquement les tâches d'aujourd'hui filtrées par le mode actif (Master ou Bureau)
     */
/**
     * Affiche uniquement les tâches d'aujourd'hui filtrées par le mode actif (Master ou Bureau)
     */
/**
     * Affiche uniquement les tâches d'aujourd'hui filtrées par le mode actif (Master ou Bureau)
     */
    public function index()
    {
        // 🛡️ VERROU DE SÉCURITÉ : Si la table tasks ou projects est en cours de création, on évite le crash 500
        if (!\Schema::hasTable('tasks') || !\Schema::hasTable('projects')) {
            return response("<div style='text-align:center;font-family:sans-serif;margin-top:10%;'>
                <h2>Initialisation du Cockpit en cours... 🚀</h2>
                <p>La base de données se configure automatiquement. Veuillez patienter.</p>
                <script>setTimeout(function(){ window.location.reload(); }, 5000);</script>
            </div>", 200)->header('Content-Type', 'text/html');
        }

        // Heure et date du Sénégal
        $today = Carbon::today('Africa/Dakar')->toDateString();
        $now = Carbon::now('Africa/Dakar')->format('H:i:s');

        // Récupération du mode utilisateur actif (par défaut 'office')
        $currentMode = session('user_mode', 'office');

        // On affiche les tâches d'aujourd'hui filtrées par le mode (Master ou Bureau)
        $todayTasks = Task::with(['category', 'project', 'step', 'examPrep'])
            ->where('type', $currentMode)
            ->whereDate('date_prevue', $today)
            ->where(function($query) use ($now) {
                $query->where('heure_fin', '>=', $now)
                      ->orWhereNull('heure_fin');
            })
            ->orderBy('heure_debut', 'asc')
            ->get();

        // Si on est en mode Master, on calcule l'état global de préparation aux examens de la journée
        $examStats = null;
        if ($currentMode === 'master') {
            $totalSubjects = $todayTasks->count();
            if ($totalSubjects > 0) {
                $reviewed = $todayTasks->where('examPrep.course_reviewed', true)->count();
                $summaries = $todayTasks->where('examPrep.summary_done', true)->count();
                $exercises = $todayTasks->where('examPrep.exercises_done', true)->count();
                $papers = $todayTasks->where('examPrep.past_papers_done', true)->count();

                $globalProgress = (($reviewed + $summaries + $exercises + $papers) / ($totalSubjects * 4)) * 100;
                $examStats = [
                    'global_progress' => round($globalProgress),
                    'reviewed' => $reviewed,
                    'summaries' => $summaries,
                    'exercises' => $exercises,
                    'papers' => $papers,
                    'total' => $totalSubjects
                ];
            }
        }

        // 🖼️ On récupère le dernier emploi du temps
        $currentSchedule = Schedule::latest()->first();

        // On récupère tous les projets et leurs étapes pour alimenter les listes déroulantes
        $projects = Project::with('steps')->orderBy('name')->get();

        return view('tasks.index', compact('todayTasks', 'currentSchedule', 'currentMode', 'examStats', 'projects'));
    }
            /**
     * Bascule entre le mode Bureau (office) et le mode Master (master)
     */
    public function switchMode($mode)
    {
        if (in_array($mode, ['master', 'office'])) {
            session(['user_mode' => $mode]);
        }
        return redirect()->route('tasks.index');
    }

    /**
     * Met à jour les étapes de révision d'un cours spécifique (Master)
     */
    public function updateExamPrep(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        $prep = ExamPrep::firstOrCreate(['task_id' => $task->id]);

        $prep->update([
            'course_reviewed' => $request->has('course_reviewed'),
            'summary_done' => $request->has('summary_done'),
            'exercises_done' => $request->has('exercises_done'),
            'past_papers_done' => $request->has('past_papers_done'),
        ]);

        return back()->with('success', 'Suivi de révision mis à jour avec succès !');
    }

    /**
     * Tableau de bord avec indicateurs filtrés selon le mode actif
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today('Africa/Dakar')->toDateString();
        $now = Carbon::now('Africa/Dakar')->format('H:i:s');
        $filter = $request->query('filter');
        $currentMode = session('user_mode', 'office');

        // Requête de base filtrée par le type de cockpit actif (Master ou Bureau)
        $baseQuery = Task::where('type', $currentMode);

        $lateQuery = (clone $baseQuery)->where(function($query) use ($today, $now) {
            $query->whereDate('date_prevue', '<', $today)
                ->orWhere(function($q) use ($today, $now) {
                    $q->whereDate('date_prevue', $today)
                        ->where('heure_fin', '<', $now);
                });
        });

        $countLate = $lateQuery->count();
        $countFuture = (clone $baseQuery)->whereDate('date_prevue', '>', $today)->count();
        $countNoDate = (clone $baseQuery)->whereNull('date_prevue')->count();

        $tasks = collect();
        if ($filter === 'late') {
            $tasks = $lateQuery->orderBy('date_prevue', 'desc')->orderBy('heure_fin', 'desc')->get();
        } elseif ($filter === 'future') {
            $tasks = (clone $baseQuery)->whereDate('date_prevue', '>', $today)->orderBy('date_prevue', 'asc')->get();
        } elseif ($filter === 'nodate') {
            $tasks = (clone $baseQuery)->whereNull('date_prevue')->get();
        }

        return view('tasks.dashboard', compact('countLate', 'countFuture', 'countNoDate', 'filter', 'tasks', 'now', 'currentMode'));
    }

    /**
     * Page de Veille Technologique
     */
    public function veilleTech()
    {
        $url = "https://techcrunch.com/feed/";
        $articles = [];
        
        try {
            $content = file_get_contents($url);
            $xml = simplexml_load_string($content);
            
            if ($xml) {
                foreach ($xml->channel->item as $item) {
                    $articles[] = [
                        'title' => (string) $item->title,
                        'description' => strip_tags((string) $item->description),
                        'link' => (string) $item->link,
                        'date' => date('d/m/Y à H:i', strtotime((string) $item->pubDate)),
                    ];
                    
                    if (count($articles) >= 10) break;
                }
            }
        } catch (\Exception $e) {
            $articles = [];
        }

        return view('tasks.veille', compact('articles'));
    }

    /**
     * Formulaire de création d'une tâche
     */
    public function create(Request $request)
    {
        $categories = Category::orderBy('name')->get()->unique('name');
        $projects = \Schema::hasTable('projects') 
            ? Project::with('steps')->orderBy('name')->get() 
            : collect();
        $prefilledTitle = $request->query('title', '');
        $currentMode = session('user_mode', 'office');

        return view('tasks.create', compact('categories', 'projects', 'prefilledTitle', 'currentMode'));
    }

    /**
     * Formulaire de modification d'une tâche
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $categories = Category::orderBy('name')->get()->unique('name');
        $projects = \Schema::hasTable('projects') 
            ? Project::with('steps')->orderBy('name')->get() 
            : collect();
        $currentMode = session('user_mode', 'office');

        return view('tasks.edit', compact('task', 'categories', 'projects', 'currentMode'));
    }

    /**
     * Enregistrement d'une nouvelle tâche
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable', // Sera géré manuellement pour valider si 'new' ou ID numérique
            'project_step_id' => 'nullable', // Sera géré manuellement pour valider si 'new' ou ID numérique
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin' => 'nullable|after:heure_debut',
            'document_link' => 'nullable|url',
            'type' => 'required|in:master,office',
            'document_status' => 'required|in:none,todo,in_progress,done',
            'new_project_name' => 'nullable|required_if:project_id,new|string|max:255',
            'new_step_name' => 'nullable|required_if:project_step_id,new|string|max:255',
        ], [
            'heure_fin.after' => 'L\'heure de fin doit obligatoirement être supérieure à l\'heure de début.',
        ]);

        $projectId = $request->project_id;
        $stepId = $request->project_step_id;

        // Création à la volée uniquement si on est en mode Bureau
        if ($request->type === 'office') {
            if ($projectId === 'new' && $request->filled('new_project_name')) {
                $project = Project::create([
                    'name' => $request->new_project_name
                ]);
                $projectId = $project->id;
            }

            if (($stepId === 'new' || $request->filled('new_step_name')) && $projectId) {
                $step = ProjectStep::create([
                    'project_id' => $projectId,
                    'name' => $request->new_step_name
                ]);
                $stepId = $step->id;
            }
        }

        // Si ce ne sont pas de nouvelles valeurs créées à la volée, on s'assure de les repasser à null si vides
        $projectId = is_numeric($projectId) ? $projectId : null;
        $stepId = is_numeric($stepId) ? $stepId : null;

        $task = Task::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'project_id' => $projectId,
            'project_step_id' => $stepId,
            'priority' => $request->priority,
            'date_prevue' => $request->date_prevue,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'document_link' => $request->document_link,
            'type' => $request->type,
            'document_status' => $request->document_status,
            'progress' => 0,
            'status' => 'todo'
        ]);

        // Si c'est une tâche Master, on initialise automatiquement sa fiche de préparation aux examens
        if ($task->type === 'master') {
            ExamPrep::create([
                'task_id' => $task->id,
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Activité créée avec succès !');
    }

    /**
     * Mise à jour d'une tâche existante
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable',
            'project_step_id' => 'nullable',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin' => 'nullable|after:heure_debut',
            'document_link' => 'nullable|url',
            'type' => 'required|in:master,office',
            'document_status' => 'required|in:none,todo,in_progress,done',
            'new_project_name' => 'nullable|required_if:project_id,new|string|max:255',
            'new_step_name' => 'nullable|required_if:project_step_id,new|string|max:255',
        ], [
            'heure_fin.after' => 'L\'heure de fin doit obligatoirement être supérieure à l\'heure de début.',
        ]);
        
        try {
            $task = Task::findOrFail($id);
            $progress = $request->progress ?? 0;
            $status = $progress == 100 ? 'done' : ($progress > 0 ? 'doing' : 'todo');

            $projectId = $request->project_id;
            $stepId = $request->project_step_id;

            if ($request->type === 'office') {
                if ($projectId === 'new' && $request->filled('new_project_name')) {
                    $project = Project::create([
                        'name' => $request->new_project_name
                    ]);
                    $projectId = $project->id;
                }

                if (($stepId === 'new' || $request->filled('new_step_name')) && $projectId) {
                    $step = ProjectStep::create([
                        'project_id' => $projectId,
                        'name' => $request->new_step_name
                    ]);
                    $stepId = $step->id;
                }
            }

            $projectId = is_numeric($projectId) ? $projectId : null;
            $stepId = is_numeric($stepId) ? $stepId : null;

            $task->update([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'project_id' => $projectId,
                'project_step_id' => $stepId,
                'priority' => $request->priority,
                'date_prevue' => $request->date_prevue,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => $request->heure_fin,
                'document_link' => $request->document_link,
                'type' => $request->type,
                'document_status' => $request->document_status,
                'progress' => $progress,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('success', 'Erreur lors de la modification : ' . $e->getMessage());
        }

        return redirect()->route('tasks.index')->with('success', 'Tâche modifiée avec succès.');
    }

    /**
     * Suppression d'une tâche
     */
    public function destroy($id)
    {
        try {
            Task::destroy($id);
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('success', 'Erreur lors de la suppression.');
        }
        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès.');
    }
}