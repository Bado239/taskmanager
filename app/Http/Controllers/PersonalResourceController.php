<?php

namespace App\Http\Controllers;

use App\Models\PersonalResource;
use Illuminate\Http\Request;

class PersonalResourceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:book,vocab,culture,quote', // adapte selon tes besoins
            'title' => 'required|string|max:255',
            'author_or_source' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        PersonalResource::create([
            'type' => $request->type,
            'title' => $request->title,
            'author_or_source' => $request->author_or_source,
            'description' => $request->description,
            'status' => 'to_do', // ou 'to_read' pour les livres
            'is_active' => true,
        ]);

        return redirect()->route('dashboard', ['view' => 'personal'])
            ->with('success', 'Ressource personnelle ajoutée avec succès !');
    }
}