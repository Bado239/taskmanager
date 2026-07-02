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
     * PAGE D'ACCUEIL : Focus Journée
     */
    public function index()
    {
        $today = Carbon::today();

        // On affiche uniquement les tâches planifiées pour aujourd'hui
        $todayTasks = Task::with(['category', 'project'])
            ->whereDate('date_prevue', $today)
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 END")
            ->get();

        return view('tasks.index', compact('todayTasks'));
    }

    /**
     * TABLEAU DE BORD : Indicateurs & listes filtrées
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $filter = $request->query('filter');

        // Compteur 1 : En retard (date passée et pas encore terminée)
        $countLate = Task::whereDate('date_prevue', '<', $today)
            ->where('status', '!=', 'done')
            ->count();

        // Compteur 2 : À venir
        $countFuture = Task::whereDate('date_prevue', '>', $today)->count();

        // Compteur 3 : Sans date
        $countNoDate = Task::whereNull('date_prevue')->count();

        // Chargement selon le filtre
        $tasks = collect();
        if ($filter === 'late') {
            $tasks = Task::with(['category', 'project'])
                ->whereDate('date_prevue', '<', $today)
                ->where('status', '!=', 'done')
                ->orderBy('date_prevue', 'desc')
                ->get();
        } elseif ($filter === 'future') {
            $tasks = Task::with(['category', 'project'])
                ->whereDate('date_prevue', '>', $today)
                ->orderBy('date_prevue', 'asc')
                ->get();
        } elseif ($filter === 'nodate') {
            $tasks = Task::with(['category', 'project'])
                ->whereNull('date_prevue')
                ->orderBy('title', 'asc')
                ->get();
        }

        return view('tasks.dashboard', compact('countLate', 'countFuture', 'countNoDate', 'tasks', 'filter'));
    }

    public function create()
    {
        return view('tasks.create', [
            'categories' => Category::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_prevue' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:high,medium,low',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $request->progress ?? 0;
        
        // Détermination automatique du statut
        $status = 'todo';
        if ($progress == 100) $status = 'done';
        elseif ($progress > 0) $status = 'doing';

        Task::create([
            'title' => $request->title,
            'date_prevue' => $request->date_prevue,
            'category_id' => $request->category_id,
            'project_id' => $request->project_id,
            'priority' => $request->priority,
            'progress' => $progress,
            'status' => $status,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Activité créée avec succès.');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.edit', [
            'task' => $task,
            'categories' => Category::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_prevue' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:high,medium,low',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $task = Task::findOrFail($id);
        $progress = $request->progress ?? 0;

        $status = 'todo';
        if ($progress == 100) $status = 'done';
        elseif ($progress > 0) $status = 'doing';

        $task->update([
            'title' => $request->title,
            'date_prevue' => $request->date_prevue,
            'category_id' => $request->category_id,
            'project_id' => $request->project_id,
            'priority' => $request->priority,
            'progress' => $progress,
            'status' => $status,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Activité modifiée avec succès.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        $previousUrl = url()->previous();

        if (str_contains($previousUrl, route('tasks.dashboard'))) {
            return redirect()->to($previousUrl)->with('success', 'Activité supprimée avec succès.');
        }

        return redirect()->route('tasks.index')->with('success', 'Activité supprimée avec succès.');
    }
}