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

            'task_id' => 'required|exists:tasks,id',

            'title' => 'required|string|max:255',

            'type' => 'required|in:pdf,link',

            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',

            'url' => 'nullable|url',

        ]);



        $filePath = null;



        // Si c'est un fichier PDF ou Word
        if ($request->hasFile('file')) {


            $filePath = $request
                ->file('file')
                ->store('documents', 'public');



            // Vérification que le fichier existe bien
            if (!Storage::disk('public')->exists($filePath)) {

                return back()->with(
                    'error',
                    'Le fichier n’a pas pu être enregistré.'
                );

            }

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
            'Document ajouté avec succès.'
        );

    }

}