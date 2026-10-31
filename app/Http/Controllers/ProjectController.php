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



        \Illuminate\Support\Facades\DB::table('projects')->insert([
            'title' => trim($data['title']),
            'type' => 'master',
            'is_active' => \Illuminate\Support\Facades\DB::raw('TRUE'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('dashboard', ['view' => 'master'])
            ->with('success', 'Dossier créé avec succès !');
    }


    public function destroy(Project $project)
    {
        if ($project->type !== 'master') {
            abort(403, 'Suppression réservée aux dossiers Master.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($project) {

            // Supprimer toutes les tâches du dossier.
            $project->tasks()->delete();

            // Supprimer définitivement le dossier.
            $project->delete();
        });

        return redirect()
            ->route('dashboard', ['view' => 'master'])
            ->with('success', 'Dossier et tâches supprimés définitivement.');
    }
}