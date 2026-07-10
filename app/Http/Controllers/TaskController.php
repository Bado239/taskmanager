<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Affiche uniquement les tâches d'aujourd'hui
     */
    public function index()
    {
        // Heure et date du Sénégal
        $today = \Carbon\Carbon::today('Africa/Dakar')->toDateString();
        $now = \Carbon\Carbon::now('Africa/Dakar')->format('H:i:s');

        // On affiche les tâches d'aujourd'hui en cours OU à venir (heure de fin non dépassée ou non définie)
        $todayTasks = \App\Models\Task::whereDate('date_prevue', $today)
            ->where(function($query) use ($now) {
                $query->where('heure_fin', '>=', $now)
                    ->orWhereNull('heure_fin');
            })
            ->orderBy('heure_debut', 'asc')
            ->get();

        return view('tasks.index', compact('todayTasks'));
    }

    /**
     * Tableau de bord avec indicateurs
     */
    public function dashboard(\Illuminate\Http\Request $request)
    {
        $today = \Carbon\Carbon::today('Africa/Dakar')->toDateString();
        $now = \Carbon\Carbon::now('Africa/Dakar')->format('H:i:s');
        $filter = $request->query('filter');

        $lateQuery = \App\Models\Task::where(function($query) use ($today, $now) {
            $query->whereDate('date_prevue', '<', $today)
                ->orWhere(function($q) use ($today, $now) {
                    $q->whereDate('date_prevue', $today)
                        ->where('heure_fin', '<', $now);
                });
        });

        $countLate = $lateQuery->count();
        $countFuture = \App\Models\Task::whereDate('date_prevue', '>', $today)->count();
        $countNoDate = \App\Models\Task::whereNull('date_prevue')->count();

        $tasks = collect();
        if ($filter === 'late') {
            $tasks = $lateQuery->orderBy('date_prevue', 'desc')->orderBy('heure_fin', 'desc')->get();
        } elseif ($filter === 'future') {
            $tasks = \App\Models\Task::whereDate('date_prevue', '>', $today)->orderBy('date_prevue', 'asc')->get();
        } elseif ($filter === 'nodate') {
            $tasks = \App\Models\Task::whereNull('date_prevue')->get();
        }

        return view('tasks.dashboard', compact('countLate', 'countFuture', 'countNoDate', 'filter', 'tasks', 'now'));
    }

    /**
     * Page de Veille Technologique (Scraping via flux RSS)
     */
    public function veilleTech()
    {
        // URL du flux RSS de TechCrunch
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
        $categories = \App\Models\Category::orderBy('name')->get()->unique('name');
        $projects = \App\Models\Project::orderBy('title')->get()->unique('title');

        // Permet de pré-remplir le titre si on vient de la page Veille Tech
        $prefilledTitle = $request->query('title', '');

        return view('tasks.create', compact('categories', 'projects', 'prefilledTitle'));
    }

    /**
     * Formulaire de modification d'une tâche
     */
    public function edit($id)
    {
        $task = \App\Models\Task::findOrFail($id);
        
        $categories = \App\Models\Category::orderBy('name')->get()->unique('name');
        $projects = \App\Models\Project::orderBy('title')->get()->unique('title');

        return view('tasks.edit', compact('task', 'categories', 'projects'));
    }

    /**
     * Enregistrement d'une nouvelle tâche
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin' => 'nullable|after:heure_debut',
        ], [
            'heure_fin.after' => 'L\'heure de fin doit obligatoirement être supérieure à l\'heure de début.',
        ]);

        $validated['progress'] = 0;

        \App\Models\Task::create($validated);

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
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin' => 'nullable|after:heure_debut',
        ], [
            'heure_fin.after' => 'L\'heure de fin doit obligatoirement être supérieure à l\'heure de début.',
        ]);
        
        try {
            $task = Task::findOrFail($id);
            $progress = $request->progress ?? 0;
            $status = $progress == 100 ? 'done' : ($progress > 0 ? 'doing' : 'todo');

            $task->update([
                'title' => $request->title,
                'category_id' => $request->category_id,
                'project_id' => $request->project_id ?: null,
                'priority' => $request->priority,
                'date_prevue' => $request->date_prevue,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => $request->heure_fin,
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