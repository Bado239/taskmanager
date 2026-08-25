<?php

namespace App\Http\Controllers;

use App\Models\PersonalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;


class PersonalResourceController extends Controller
{


    /**
     * Ajouter livre PDF ou vocabulaire
     */
    public function store(Request $request)
    {

        $validated = $request->validate([

            'type' => [
                'required',
                'string',
                'max:50'
            ],

            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'author_or_source' => [
                'nullable',
                'string',
                'max:255'
            ],

            'pdf_file' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:20480'
            ],

        ]);



        try {


            $pdfUrl = null;



            /*
            |--------------------------------------------------------------------------
            | Upload PDF Supabase Storage
            |--------------------------------------------------------------------------
            */

            if(
                $validated['type'] === 'book'
                &&
                $request->hasFile('pdf_file')
            ){


                $path = $request
                    ->file('pdf_file')
                    ->store(
                        'pdfs',
                        's3'
                    );



                if(!$path){

                    throw new \Exception(
                        "Impossible d'envoyer le fichier PDF."
                    );

                }



                $pdfUrl =

                    rtrim(
                        env('SUPABASE_URL'),
                        '/'
                    )

                    . '/storage/v1/object/public/'

                    . env(
                        'AWS_BUCKET',
                        'ebooks'
                    )

                    . '/'

                    . $path;

            }




            /*
            |--------------------------------------------------------------------------
            | Création ressource
            |--------------------------------------------------------------------------
            */


            PersonalResource::create([


                'type' => $validated['type'],


                'title' => $validated['title'],


                'description' =>
                    $validated['description']
                    ?? null,


                'author_or_source' =>
                    $validated['author_or_source']
                    ?? null,


                'pdf_path' => $pdfUrl,


                'status' => 'to_read',


                'is_active' => true,


            ]);





            return redirect()

                ->route(
                    'dashboard',
                    [
                        'view' => 'personal'
                    ]
                )

                ->with(
                    'success',
                    'Ressource ajoutée avec succès.'
                );


        }


        catch(Throwable $e){


            return back()

                ->withInput()

                ->with(
                    'error',
                    'Erreur ajout : '
                    . $e->getMessage()
                );

        }

    }





    /**
     * Lecture PDF
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
     * Mise à jour livre
     */
    public function update(
        Request $request,
        $id
    )
    {

        $resource =
            PersonalResource::findOrFail($id);



        $resource->update([

            'notes' =>
                $request->notes,


            'status' =>
                $request->status,

        ]);



        return back()

            ->with(
                'success',
                'Modification enregistrée.'
            );

    }





    /**
     * Suppression ressource
     */
    public function destroy($id)
    {

        try{


            $resource =
                PersonalResource::findOrFail($id);




            /*
            |--------------------------------------------------------------------------
            | Suppression PDF Supabase
            |--------------------------------------------------------------------------
            */


            if($resource->pdf_path){


                $path = parse_url(
                    $resource->pdf_path,
                    PHP_URL_PATH
                );



                $path = str_replace(

                    '/storage/v1/object/public/ebooks/',

                    '',

                    $path

                );



                if($path){

                    Storage::disk('s3')
                        ->delete($path);

                }

            }





            $resource->delete();




            return redirect()

                ->route(
                    'dashboard',
                    [
                        'view'=>'personal'
                    ]
                )

                ->with(
                    'success',
                    'Suppression réussie.'
                );


        }


        catch(Throwable $e){


            return back()

                ->with(
                    'error',
                    'Erreur suppression : '
                    .$e->getMessage()
                );

        }

    }



    /**
     * Sauvegarde progression lecture PDF
     */
    public function progress(Request $request, $id)
    {

        $book = PersonalResource::findOrFail($id);



        $book->update([

            'current_page' => $request->current_page ?? 1,

            'progress' => $request->progress ?? 0,

        ]);



        return response()->json([

            'success' => true,

            'current_page' => $book->current_page,

            'progress' => $book->progress,

        ]);

    }


}