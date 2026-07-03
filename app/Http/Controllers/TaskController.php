<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Affiche uniquement les tâches d'aujourd'hui
     */

// 1. La page principale (Uniquement Aujourd'hui)
// 1. Page principale : uniquement les tâches d'aujourd'hui

    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();
        // On s'assure d'utiliser le bon fuseau horaire
        $now = \Carbon\Carbon::now('Europe/Paris')->format('H:i:s');

        // On affiche les tâches d'aujourd'hui en cours ou à venir
        $todayTasks = \App\Models\Task::whereDate('date_prevue', $today)
            ->where(function($query) use ($now) {
                $query->where('heure_fin', '>=', $now) // Pas encore dépassée
                    ->orWhereNull('heure_fin');
            })
            ->orderBy('heure_debut', 'asc')
            ->get();

        return view('tasks.index', compact('todayTasks'));
    }

    public function dashboard(\Illuminate\Http\Request $request)
    {
        $today = \Carbon\Carbon::today()->toDateString();
        $now = \Carbon\Carbon::now('Europe/Paris')->format('H:i:s');
        $filter = $request->query('filter');

        // Les tâches passées : uniquement si l'heure de fin est strictement inférieure à l'heure actuelle
        $lateQuery = \App\Models\Task::where(function($query) use ($today, $now) {
            $query->whereDate('date_prevue', '<', $today)
                ->orWhere(function($q) use ($today, $now) {
                    $q->whereDate('date_prevue', $today)
                        ->where('heure_fin', '<', $now); // Heure de fin passée
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
     * Formulaire de création (Génère des catégories virtuelles si la base est vide/lecture seule)
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();

        // Si la base est vide (ex: restriction Render), on crée des structures virtuelles en mémoire
        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['id' => 1, 'name' => 'Professionnel'],
                (object) ['id' => 2, 'name' => 'Personnel'],
                (object) ['id' => 3, 'name' => 'Études'],
            ]);
        }

        if ($projects->isEmpty()) {
            $projects = collect([
                (object) ['id' => 1, 'title' => 'Développement Web'],
                (object) ['id' => 2, 'title' => 'Organisation'],
            ]);
        }

        return view('tasks.create', [
            'categories' => $categories,
            'projects' => $projects,
        ]);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'project_id' => 'nullable|exists:projects,id',
        'priority' => 'required|in:high,medium,low',
        'date_prevue' => 'nullable|date',
        'heure_debut' => 'nullable',
        'heure_fin' => 'nullable',
    ]);

    // On force la progression par défaut à 0 lors de la création
    $validated['progress'] = 0;

    \App\Models\Task::create($validated);

    return redirect()->route('tasks.index')->with('success', 'Activité créée avec succès !');
}

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $projects = Project::orderBy('title')->get();

        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['id' => 1, 'name' => 'Professionnel'],
                (object) ['id' => 2, 'name' => 'Personnel'],
                (object) ['id' => 3, 'name' => 'Études'],
            ]);
        }

        if ($projects->isEmpty()) {
            $projects = collect([
                (object) ['id' => 1, 'title' => 'Développement Web'],
                (object) ['id' => 2, 'title' => 'Organisation'],
            ]);
        }

        return view('tasks.edit', [
            'task' => $task,
            'categories' => $categories,
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        try {
            $task = Task::findOrFail($id);
            $progress = $request->progress ?? 0;
            $status = $progress == 100 ? 'done' : ($progress > 0 ? 'doing' : 'todo');

            $task->update([
                'title' => $request->title,
                'category_id' => $request->category_id ?: null,
                'project_id' => $request->project_id ?: null,
                'priority' => $request->priority,
                'date_prevue' => $request->date_prevue,
                'progress' => $progress,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('success', 'Modification simulée (Base en lecture seule).');
        }

        return redirect()->route('tasks.index')->with('success', 'Tâche modifiée avec succès.');
    }

    public function destroy($id)
    {
        try {
            Task::destroy($id);
        } catch (\Exception $e) {
            return redirect()->route('tasks.index')->with('success', 'Suppression simulée (Base en lecture seule).');
        }
        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès.');
    }
}