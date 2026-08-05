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

    public function destroy($id)
    {
        $projet = Projet::find($id);

        if (!$projet) {
            return redirect()->route('dashboard')->with('error', 'Projet introuvable ou déjà supprimé.');
        }

        // Dissocier les tâches associées si la relation existe
        if (method_exists($projet, 'tasks')) {
            $projet->tasks()->update(['project_id' => null]);
        }

        $projet->delete();

        return redirect()->route('dashboard')->with('success', 'Projet supprimé avec succès.');
    }
}