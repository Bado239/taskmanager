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
     * Tableau principal des tâches
     */
    public function index()
    {
        $today = Carbon::today();

        // Compatible SQLite + MySQL
        $priorityOrder = "
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
                ELSE 4
            END
        ";

        // ============================
        // Tâches d'aujourd'hui
        // ============================

        $todayTasks = Task::with(['category','project'])
            ->whereDate('date_prevue', $today)
            ->orderByRaw($priorityOrder)
            ->orderBy('title')
            ->get();

        // ============================
        // Tâches en retard
        // ============================

        $lateTasks = Task::with(['category','project'])
            ->whereNotNull('date_prevue')
            ->whereDate('date_prevue','<',$today)
            ->where('status','!=','done')
            ->orderBy('date_prevue')
            ->orderByRaw($priorityOrder)
            ->get();

        // ============================
        // Tâches à venir
        // ============================

        $futureTasks = Task::with(['category','project'])
            ->whereDate('date_prevue','>',$today)
            ->orderBy('date_prevue')
            ->orderByRaw($priorityOrder)
            ->get();

        // ============================
        // Tâches sans date
        // ============================

        $noDateTasks = Task::with(['category','project'])
            ->whereNull('date_prevue')
            ->orderByRaw($priorityOrder)
            ->orderBy('title')
            ->get();

        return view('tasks.index', compact(
            'todayTasks',
            'lateTasks',
            'futureTasks',
            'noDateTasks'
        ));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('tasks.create', [
            'categories' => Category::orderBy('name')->get(),
            'projects'   => Project::orderBy('title')->get(),
        ]);
    }

    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|max:255',
            'category_id'  => 'required|exists:categories,id',
            'project_id'   => 'nullable|exists:projects,id',
            'priority'     => 'required|in:high,medium,low',
            'date_prevue'  => 'nullable|date',
            'progress'     => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $request->progress ?? 0;

        if ($progress == 100) {
            $status = 'done';
        } elseif ($progress > 0) {
            $status = 'doing';
        } else {
            $status = 'todo';
        }

        Task::create([
            'title'        => $request->title,
            'category_id'  => $request->category_id,
            'project_id'   => $request->project_id,
            'priority'     => $request->priority,
            'date_prevue'  => $request->date_prevue,
            'progress'     => $progress,
            'status'       => $status,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success','Tâche créée avec succès.');
    }

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);

        return view('tasks.edit',[
            'task' => $task,
            'categories' => Category::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ]);
    }

    /**
     * Mise à jour
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'title'        => 'required|max:255',
            'category_id'  => 'required|exists:categories,id',
            'project_id'   => 'nullable|exists:projects,id',
            'priority'     => 'required|in:high,medium,low',
            'date_prevue'  => 'nullable|date',
            'progress'     => 'nullable|integer|min:0|max:100',
        ]);

        $task = Task::findOrFail($id);

        $progress = $request->progress ?? 0;

        if ($progress == 100) {
            $status = 'done';
        } elseif ($progress > 0) {
            $status = 'doing';
        } else {
            $status = 'todo';
        }

        $task->update([
            'title'        => $request->title,
            'category_id'  => $request->category_id,
            'project_id'   => $request->project_id,
            'priority'     => $request->priority,
            'date_prevue'  => $request->date_prevue,
            'progress'     => $progress,
            'status'       => $status,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success','Tâche modifiée avec succès.');
    }

    /**
     * Suppression
     */
    public function destroy($id)
    {
        Task::destroy($id);

        return redirect()
            ->route('tasks.index')
            ->with('success','Tâche supprimée.');
    }
}