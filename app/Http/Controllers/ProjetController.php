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
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut'  => 'nullable|date',
            'date_fin'    => 'nullable|date|after_or_equal:date_debut',
        ]);

        Projet::create($validated);

        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    public function destroy(Projet $projet)
    {
        // Dissocier les tâches associées si la relation existe
        if (method_exists($projet, 'tasks')) {
            $projet->tasks()->update(['project_id' => null]);
        }

        $projet->delete();

        return redirect()->back()->with('success', 'Projet supprimé avec succès.');
    }
}