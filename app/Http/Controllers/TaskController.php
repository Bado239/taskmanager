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
     * Affiche uniquement les tâches d'aujourd'hui dont l'heure de fin n'est pas encore dépassée.
     */
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now()->format('H:i:s');

        // On affiche uniquement les tâches d'aujourd'hui qui ne sont pas terminées/dépassées
        $todayTasks = Task::with(['category', 'project'])
            ->whereDate('date_prevue', $today)
            ->where(function($query) use ($now) {
                $query->where('heure_fin', '>=', $now)
                      ->orWhereNull('heure_fin');
            })
            ->orderBy('heure_debut', 'asc')
            ->get();

        return view('tasks.index', compact('todayTasks', 'now'));
    }

    /**
     * TABLEAU DE BORD : Indicateurs de Performance & Archivage temporel
     */
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $now = Carbon::now()->format('H:i:s');
        $filter = $request->query('filter');

        // Compteur 1 : Activités passées ou en retard (date antérieure, ou aujourd'hui avec heure de fin dépassée)
        $countLate = Task::where(function($q) use ($today, $now) {
                $q->whereDate('date_prevue', '<', $today)
                  ->orWhere(function($subQ) use ($today, $now) {
                      $subQ->whereDate('date_prevue', $today)->where('heure_fin', '<', $now);
                  });
            })
            ->count();

        // Compteur 2 : Activités prévues pour les jours à venir
        $countFuture = Task::whereDate('date_prevue', '>', $today)->count();

        // Compteur 3 : Activités de fond (sans date de planification)
        $countNoDate = Task::whereNull('date_prevue')->count();

        // Chargement de la liste dynamique selon le filtre sélectionné
        $tasks = collect();
        if ($filter === 'late') {
            $tasks = Task::with(['category', 'project'])
                ->where(function($q) use ($today, $now) {
                    $q->whereDate('date_prevue', '<', $today)
                      ->orWhere(function($subQ) use ($today, $now) {
                          $subQ->whereDate('date_prevue', $today)->where('heure_fin', '<', $now);
                      });
                })
                ->orderBy('date_prevue', 'desc')
                ->orderBy('heure_fin', 'desc')
                ->get();
        } elseif ($filter === 'future') {
            $tasks = Task::with(['category', 'project'])
                ->whereDate('date_prevue', '>', $today)
                ->orderBy('date_prevue', 'asc')
                ->orderBy('heure_debut', 'asc')
                ->get();
        } elseif ($filter === 'nodate') {
            $tasks = Task::with(['category', 'project'])
                ->whereNull('date_prevue')
                ->orderBy('title', 'asc')
                ->get();
        }

        return view('tasks.dashboard', compact('countLate', 'countFuture', 'countNoDate', 'tasks', 'filter'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('tasks.create', [
            'categories' => Category::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ]);
    }

    /**
     * Enregistrement d'une nouvelle activité
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date_prevue' => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Activité créée avec succès.');
    }

    /**
     * Formulaire de modification
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);

        return view('tasks.edit', [
            'task' => $task,
            'categories' => Category::orderBy('name')->get(),
            'projects' => Project::orderBy('title')->get(),
        ]);
    }

    /**
     * Mise à jour de l'activité
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date_prevue' => 'nullable|date',
            'heure_debut' => 'nullable',
            'heure_fin' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $task = Task::findOrFail($id);
        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Activité modifiée avec succès.');
    }

    /**
     * Suppression définitive
     */
    public function destroy(Task $task)
    {
        $task->delete();
        $previousUrl = url()->previous();

        // Reste sur le Dashboard si la suppression a été faite depuis celui-ci
        if (str_contains($previousUrl, route('tasks.dashboard'))) {
            return redirect()->to($previousUrl)->with('success', 'Activité supprimée avec succès.');
        }

        return redirect()->route('tasks.index')->with('success', 'Activité supprimée avec succès.');
    }
}