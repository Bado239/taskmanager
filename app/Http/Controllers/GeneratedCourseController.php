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


        // Lire les documents existants s'il y en a

        foreach($task->learningDocuments as $document)
        {

            $content = $reader->read($document);


            if($content)
            {
                $text .= "\n\n".$content;
            }

        }


        // Si aucun document trouvé,
        // on crée automatiquement le sujet du cours

        if(strlen(trim($text)) < 100)
        {

            $text = "

            Créer un cours complet de niveau Master 1 au Sénégal.

            Matière :
            ".$task->project->title."

            Chapitre :
            ".$task->title."

            Le cours doit contenir :
            - Introduction
            - Définitions
            - Concepts clés
            - Développements détaillés
            - Exemples appliqués au Sénégal
            - Résumé
            - Questions de révision

            ";

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