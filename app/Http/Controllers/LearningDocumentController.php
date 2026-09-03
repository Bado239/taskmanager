<?php

namespace App\Http\Controllers;

use App\Models\LearningDocument;


class LearningDocumentController extends Controller
{


    public function view(LearningDocument $document)
    {


        $url = "https://zhlojjivmwuuqhzeqpgg.supabase.co/storage/v1/object/public/ebooks/"
        .$document->file_path;



        return view(
            'learning-documents.view',
            compact(
                'document',
                'url'
            )
        );


    }


}