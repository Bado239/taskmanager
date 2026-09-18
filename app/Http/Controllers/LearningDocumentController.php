<?php

namespace App\Http\Controllers;

use App\Models\LearningDocument;
use Illuminate\Http\Request;


class LearningDocumentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'title'   => 'required|string|max:255',
            'type'    => 'required|string|max:50',
            'url'     => 'required|url',
        ]);

        LearningDocument::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Document ajouté avec succès.');
    }


    public function view(LearningDocument $document)
    {
        $url = $document->url;

        return view(
            'learning-documents.view',
            compact('document', 'url')
        );
    }
}