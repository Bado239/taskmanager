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



        // Upload PDF / Word vers Supabase Storage
        if ($request->hasFile('file')) {


            $filePath = Storage::disk('s3')->putFile(
                'learning-documents',
                $request->file('file'),
                'public'
            );


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