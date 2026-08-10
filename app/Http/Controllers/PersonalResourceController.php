<?php

namespace App\Http\Controllers;

use App\Models\PersonalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PersonalResourceController extends Controller
{
    /**
     * Ajouter un livre / ressource personnelle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'author_or_source' => 'nullable|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        try {
            /*
             * 1. Upload du PDF dans Supabase Storage
             * Bucket : ebooks
             * Dossier : pdfs
             */
            $path = $request->file('pdf_file')->store('pdfs', 's3');

            if (!$path) {
                return back()
                    ->withInput()
                    ->with('error', "L'upload du fichier PDF a échoué.");
            }

            /*
             * 2. Construction de l'URL publique Supabase
             *
             * Dans .env :
             * SUPABASE_URL=https://xxxxxxxx.supabase.co
             * AWS_BUCKET=ebooks
             */
            $supabaseUrl = rtrim(env('SUPABASE_URL'), '/');
            $bucketName = env('AWS_BUCKET', 'ebooks');

            if (empty($supabaseUrl)) {
                throw new \Exception(
                    "La variable SUPABASE_URL n'est pas définie dans le fichier .env."
                );
            }

            $url = $supabaseUrl
                . '/storage/v1/object/public/'
                . $bucketName
                . '/'
                . $path;

            /*
             * 3. Enregistrement du livre dans la base
             */
            PersonalResource::create([
                'type' => $validated['type'],
                'title' => $validated['title'],
                'author_or_source' => $validated['author_or_source'] ?? null,
                'pdf_path' => $url,
                'status' => 'to_read',
                'is_active' => true,
            ]);

            /*
             * 4. Retour vers l'espace Développement Personnel
             */
            return redirect()
                ->route('dashboard', ['view' => 'personal'])
                ->with(
                    'success',
                    'Livre ajouté et PDF uploadé avec succès sur Supabase !'
                );

        } catch (Throwable $e) {

            /*
             * Si quelque chose bloque, on affiche l'erreur
             * au lieu d'avoir l'impression que rien ne se passe.
             */
            return back()
                ->withInput()
                ->with(
                    'error',
                    "Erreur lors de l'ajout du livre : " . $e->getMessage()
                );
        }
    }


    /**
     * Supprimer un livre
     */
    public function destroy($id)
    {
        try {

            $book = PersonalResource::findOrFail($id);

            /*
             * Supprimer également le PDF de Supabase Storage
             */
            if ($book->pdf_path) {

                $path = parse_url($book->pdf_path, PHP_URL_PATH);

                if ($path) {

                    $prefix = '/storage/v1/object/public/ebooks/';

                    $relativePath = str_replace(
                        $prefix,
                        '',
                        $path
                    );

                    if (!empty($relativePath)) {
                        Storage::disk('s3')->delete($relativePath);
                    }
                }
            }

            $book->delete();

            return redirect()
                ->route('dashboard', ['view' => 'personal'])
                ->with('success', 'Livre supprimé avec succès.');

        } catch (Throwable $e) {

            return redirect()
                ->route('dashboard', ['view' => 'personal'])
                ->with(
                    'error',
                    "Erreur lors de la suppression : " . $e->getMessage()
                );
        }
    }


    /**
     * Afficher le lecteur PDF
     */
    public function show($id)
    {
        $book = PersonalResource::findOrFail($id);

        return view('book-reader', compact('book'));
    }


    /**
     * Mettre à jour les notes et le statut
     */
    public function update(Request $request, $id)
    {
        $book = PersonalResource::findOrFail($id);

        $book->update([
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Progression et notes sauvegardées !'
            );
    }
}