<?php

namespace App\Http\Controllers;

use App\Models\LearningDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningDocumentController extends Controller
{

    public function store(Request $request)
    {

        $request->validate([

            'task_id' => 'required',

            'title' => 'required',

            'type' => 'required',

            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',

            'url' => 'nullable|url',

        ]);



        $filePath = null;



        if ($request->hasFile('file')) {


            $file = $request->file('file');


            $filename = $file->hashName();


            $destination = storage_path('app/public/documents');


            // créer le dossier si nécessaire
            if (!file_exists($destination)) {

                mkdir($destination, 0755, true);

            }


            // déplacement réel du fichier
            $file->move(
                $destination,
                $filename
            );


            $filePath = 'documents/'.$filename;

        }



        LearningDocument::create([

            'task_id' => $request->task_id,

            'title' => $request->title,

            'type' => $request->type,

            'file_path' => $filePath,

            'url' => $request->url,

        ]);



        return back()->with(
            'success',
            'Document ajouté avec succès'
        );

    }
}