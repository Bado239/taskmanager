<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Http\Request;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = Projet::all();

        return view('projets.index', compact('projets'));
    }

    public function create()
    {
        return view('projets.create');
    }

    public function store(Request $request)
    {
        Projet::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ]);

        return redirect('/projets');
    }

    public function destroy(Project $project)
    {
        // Réaffecter les tâches associées à null avant suppression (optionnel)
        $project->tasks()->update(['project_id' => null]);

        $project->delete();

        return redirect()->back()->with('success', 'Projet supprimé avec succès.');
    }
}