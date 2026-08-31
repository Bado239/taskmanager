<?php

namespace App\Http\Controllers;

use App\Models\LearningDocument;
use Illuminate\Http\Request;

class LearningDocumentController extends Controller
{


    public function store(Request $request)
    {

        $request->validate([

            'title'=>'required',

            'type'=>'required',

        ]);



        LearningDocument::create([

            'task_id'=>$request->task_id,

            'title'=>$request->title,

            'type'=>$request->type,

            'url'=>$request->url,

        ]);



        return back()->with(
            'success',
            'Document ajouté'
        );

    }


}