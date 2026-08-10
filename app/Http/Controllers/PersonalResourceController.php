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
            'pdf_file' => 'required|mimes:pdf|max:20480', // Max 20Mo
        ]);

        // 1. Upload du PDF directement dans le bucket Supabase 'ebooks'
        // On le stocke dans un sous-dossier 'pdfs' pour plus de propreté
        $path = $request->file('pdf_file')->store('pdfs', 's3');

        // Vérification de sécurité
        if (!$path) {
            return back()->with('error', 'L\'upload du fichier a échoué sur Supabase.');
        }

        // 2. Construction MANUELLE de l'URL publique Supabase (infaillible)
        $supabaseProjectId = env('AWS_ACCESS_KEY_ID'); // On réutilise la variable du .env
        $bucketName = env('AWS_BUCKET'); // 'ebooks'
        
        // Format officiel Supabase : https://<projet>.supabase.co/storage/v1/object/public/<bucket>/<chemin>
        $url = "https://{$supabaseProjectId}.supabase.co/storage/v1/object/public/{$bucketName}/{$path}";

        // 3. Enregistrement en base de données
        PersonalResource::create([
            'type' => $request->type,
            'title' => $request->title,
            'author_or_source' => $request->author_or_source,
            'pdf_path' => $url, // L'URL publique et permanente du PDF
            'status' => 'to_read',
            'is_active' => true, 
        ]);

        return redirect()
            ->route('dashboard', ['view' => 'personal'])
            ->with('success', 'Livre ajouté et PDF uploadé avec succès sur Supabase !');
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