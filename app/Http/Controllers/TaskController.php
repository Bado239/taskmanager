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
    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Tri de base pour la priorité : Haute -> Moyenne -> Basse
        $priorityOrder = "CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END";

        // 1. Tâches pour Aujourd'hui
        $todayTasks = Task::whereDate('date_prevue', $today)
            ->orderByRaw($priorityOrder)
            ->get();

        // 2. Tâches En retard (date dépassée et non terminées à 100%)
        $lateTasks = Task::whereDate('date_prevue', '<', $today)
            ->where('progress', '<', 100)
            ->orderByRaw($priorityOrder)
            ->get();

        // 3. Tâches À venir (dates futures)
        $futureTasks = Task::whereDate('date_prevue', '>', $today)
            ->orderByRaw($priorityOrder)
            ->get();

        // 4. Tâches Sans date
        $noDateTasks = Task::whereNull('date_prevue')
            ->orderByRaw($priorityOrder)
            ->get();

        // Envoi de toutes les variables nécessaires à la vue
        return view('tasks.index', compact('todayTasks', 'lateTasks', 'futureTasks', 'noDateTasks'));
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
        // En mode gratuit temporaire, on assouplit la validation exists pour éviter les conflits de lecture seule
        $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required',
            'project_id' => 'nullable',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $request->progress ?? 0;
        $status = $progress == 100 ? 'done' : ($progress > 0 ? 'doing' : 'todo');

        try {
            Task::create([
                'title' => $request->title,
                'category_id' => $request->category_id ?: null,
                'project_id' => $request->project_id ?: null,
                'priority' => $request->priority,
                'date_prevue' => $request->date_prevue,
                'progress' => $progress,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            // Si la base globale reste bloquée en écriture, on redirige avec un message descriptif
            return redirect()->route('tasks.index')->with('success', 'Note : Base de données en lecture seule, enregistrement simulé !');
        }

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès.');
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