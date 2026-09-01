<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\GeneratedCourse;
use App\Services\DocumentReaderService;
use App\Services\AICourseGeneratorService;


class GeneratedCourseController extends Controller
{


    public function generate(
        Task $task,
        DocumentReaderService $reader,
        AICourseGeneratorService $ai
    )
    {

        // Charger les documents liés à la tâche
        $task->load('learningDocuments');


        $text = "";


        foreach($task->learningDocuments as $document)
        {

            $content = $reader->read($document);


            if($content)
            {
                $text .= "\n\n".$content;
            }

        }



        if(strlen(trim($text)) < 100)
        {

            return back()->with(
                'error',
                'Aucun contenu exploitable trouvé dans les documents.'
            );

        }




        try {


            $content = $ai->generate(

                $text,

                $task->project->title
                .' - '
                .$task->title

            );



            GeneratedCourse::updateOrCreate(

                [
                    'task_id'=>$task->id
                ],

                [
                    'title'=>$task->title,

                    'content'=>$content
                ]

            );



        } catch(\Exception $e) {


            return back()->with(

                'error',

                'Erreur génération IA : '.$e->getMessage()

            );


        }



        return back()->with(

            'success',

            'Cours généré avec succès.'

        );


    }


}