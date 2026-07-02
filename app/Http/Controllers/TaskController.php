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
        $today = Carbon::today();

        // Récupère uniquement les tâches prévues pour aujourd'hui
        $todayTasks = Task::with(['category', 'project'])
            ->whereDate('date_prevue', $today)
            ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
            ->get();

        return view('tasks.index', compact('todayTasks'));
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
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $request->progress ?? 0;
        $status = $progress == 100 ? 'done' : ($progress > 0 ? 'doing' : 'todo');

        Task::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'project_id' => $request->project_id,
            'priority' => $request->priority,
            'date_prevue' => $request->date_prevue,
            'progress' => $progress,
            'status' => $status,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès.');
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
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
            'priority' => 'required|in:high,medium,low',
            'date_prevue' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $task = Task::findOrFail($id);
        $progress = $request->progress ?? 0;
        $status = $progress == 100 ? 'done' : ($progress > 0 ? 'doing' : 'todo');

        $task->update([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'project_id' => $request->project_id,
            'priority' => $request->priority,
            'date_prevue' => $request->date_prevue,
            'progress' => $progress,
            'status' => $status,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tâche modifiée avec succès.');
    }

    public function destroy($id)
    {
        Task::destroy($id);
        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée avec succès.');
    }
}