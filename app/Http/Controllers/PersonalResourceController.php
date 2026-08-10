<?php

namespace App\Http\Controllers;

use App\Models\PersonalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Ajouté pour Supabase

class PersonalResourceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'author_or_source' => 'nullable|string|max:255',
            'pdf_file' => 'required|mimes:pdf|max:20480', // PDF obligatoire, max 20Mo
        ]);

        // 1. Upload du PDF directement dans le bucket Supabase 'ebooks'
        $path = $request->file('pdf_file')->store('/', 's3');

        // 2. Récupération de l'URL publique permanente générée par Supabase
        $url = Storage::disk('s3')->url($path);

        // 3. Enregistrement en base de données
        // Le $casts=['is_active' => 'boolean'] dans ton modèle va automatiquement 
        // convertir true en 'true' pour PostgreSQL, plus besoin de DB::raw !
        PersonalResource::create([
            'type' => $request->type,
            'title' => $request->title,
            'author_or_source' => $request->author_or_source,
            'pdf_path' => $url, // On stocke l'URL Supabase
            'status' => 'to_read',
            'is_active' => true, 
        ]);

        return redirect()
            ->route('dashboard', ['view' => 'personal'])
            ->with('success', 'Livre et PDF ajoutés avec succès sur Supabase !');
    }

    public function destroy($id)
    {
        $book = PersonalResource::findOrFail($id);
        
        // Optionnel : Supprimer aussi le fichier PDF de Supabase Storage quand on supprime le livre
        if ($book->pdf_path) {
            // On extrait le chemin relatif depuis l'URL complète pour le supprimer du bucket
            $path = parse_url($book->pdf_path, PHP_URL_PATH);
            $relativePath = str_replace('/storage/v1/object/public/ebooks/', '', $path);
            Storage::disk('s3')->delete($relativePath);
        }

        $book->delete();

        return redirect()
            ->route('dashboard', ['view' => 'personal'])
            ->with('success', 'Livre supprimé.');
    }

    public function show($id)
    {
        $book = PersonalResource::findOrFail($id);
        return view('book-reader', compact('book'));
    }

    public function update(Request $request, $id)
    {
        $book = PersonalResource::findOrFail($id);
        
        // Sauvegarde des notes et du statut (en cours de lecture / terminé)
        $book->update([
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Progression et notes sauvegardées !');
    }
}