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


            // Upload PDF vers Supabase Storage

            $path = $request
                ->file('pdf_file')
                ->store('pdfs', 's3');



            if (!$path) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        "L'upload du fichier PDF a échoué."
                    );

            }



            // Création URL publique Supabase

            $supabaseUrl = rtrim(
                env('SUPABASE_URL'),
                '/'
            );


            $bucketName = env(
                'AWS_BUCKET',
                'ebooks'
            );



            if (!$supabaseUrl) {

                throw new \Exception(
                    "SUPABASE_URL absent dans .env"
                );

            }



            $url = $supabaseUrl
                . '/storage/v1/object/public/'
                . $bucketName
                . '/'
                . $path;




            // Enregistrement dans PostgreSQL

            PersonalResource::create([

                'type' => $validated['type'],

                'title' => $validated['title'],

                'author_or_source' =>
                    $validated['author_or_source'] ?? null,

                'pdf_path' => $url,

                'status' => 'to_read',

                'is_active' => 'true',

            ]);




            return redirect()

                ->route('dashboard', [
                    'view' => 'personal'
                ])

                ->with(
                    'success',
                    'Livre ajouté avec succès.'
                );



        } catch (Throwable $e) {


            return back()

                ->withInput()

                ->with(
                    'error',
                    "Erreur ajout livre : "
                    . $e->getMessage()
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



            if ($book->pdf_path) {


                $path = parse_url(
                    $book->pdf_path,
                    PHP_URL_PATH
                );


                $prefix =
                    '/storage/v1/object/public/ebooks/';



                $relativePath =
                    str_replace(
                        $prefix,
                        '',
                        $path
                    );



                if ($relativePath) {

                    Storage::disk('s3')
                        ->delete($relativePath);

                }

            }



            $book->delete();



            return redirect()

                ->route('dashboard', [
                    'view' => 'personal'
                ])

                ->with(
                    'success',
                    'Livre supprimé avec succès.'
                );


        } catch (Throwable $e) {


            return redirect()

                ->route('dashboard', [
                    'view' => 'personal'
                ])

                ->with(
                    'error',
                    "Erreur suppression : "
                    . $e->getMessage()
                );

        }

    }





    /**
     * Afficher le lecteur PDF
     */
    public function show($id)
    {

        $book = PersonalResource::findOrFail($id);


        return view(
            'book-reader',
            compact('book')
        );

    }





    /**
     * Mettre à jour notes et statut
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
                'Progression sauvegardée.'
            );

    }

}