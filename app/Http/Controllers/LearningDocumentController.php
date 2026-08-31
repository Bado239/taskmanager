<?php

namespace App\Http\Controllers;

use App\Models\LearningDocument;
use Illuminate\Http\Request;

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

            $filePath = $request
                ->file('file')
                ->store('documents', 'public');

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