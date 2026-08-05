<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\Category;

class TaskController extends Controller
{
    // ... vos autres méthodes ...

    public function store(Request $request)
    {
        $projectId = $request->project_id;

        // Gestion du nouveau projet
        if ($projectId === 'new' && !empty($request->new_project_name)) {
            $newProject = Project::create([
                'title' => $request->new_project_name,
                'user_id' => auth()->id() ?? null, // Ajustez selon votre gestion d'utilisateurs
            ]);
            $projectId = $newProject->id;
        } elseif (empty($projectId) || $projectId === 'new') {
            $projectId = null;
        }

        $categoryId = $request->category_id;

        // Gestion de la nouvelle étape (catégorie)
        if ($categoryId === 'new' && !empty($request->new_category_name)) {
            $newCategory = Category::create([
                'title' => $request->new_category_name,
                // Ajoutez d'autres champs si votre modèle Category en a besoin (ex: type)
            ]);
            $categoryId = $newCategory->id;
        } elseif (empty($categoryId) || $categoryId === 'new') {
            $categoryId = null;
        }

        Task::create([
            'title'           => $request->title,
            'document_link'   => $request->document_link,
            'category_id'     => $categoryId,
            'project_id'      => $projectId,
            'document_status' => $request->document_status ?? 'todo',
            'priority'        => $request->priority ?? 'medium',
            'date_prevue'     => $request->date_prevue,
            'execution_date'  => $request->execution_date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'type'            => $request->type,
        ]);

        $targetView = in_array($request->type, ['office', 'master']) ? $request->type : 'dashboard';

        return redirect()->route('dashboard', ['view' => $targetView])
                         ->with('success', 'Tâche créée avec succès !');
    }

    // ...
}