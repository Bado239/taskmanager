<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;


class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $project = new Project();
        $project->title = trim($data['title']);
        $project->type = 'master';
        $project->is_active = true;
        $project->save();

        return redirect()
            ->route('dashboard', ['view' => 'master'])
            ->with('success', 'Dossier créé avec succès !');
    }

    public function destroy(Project $project)
    {
        try {
            // Détache les tâches liées au projet avant suppression
            $project->tasks()->update(['project_id' => null]);
            
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Projet supprimé avec succès.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.'
            ], 500);
        }
    }
}