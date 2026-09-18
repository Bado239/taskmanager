<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\GeneratedCourse;
use App\Services\StudyRaidService;
use App\Services\CourseFormatterService;


class GeneratedCourseController extends Controller
{


    public function generate(
        Task $task,
        StudyRaidService $studyRaid,
        CourseFormatterService $formatter
    )
    {

        try {


            /*
            |--------------------------------------------------------------------------
            | Récupération du cours depuis StudyRaid
            |--------------------------------------------------------------------------
            */


            $content = $studyRaid->getCourse($task);



            if(!$content)
            {

                return back()->with(

                    'error',

                    'Aucun cours StudyRaid trouvé pour ce chapitre.'

                );

            }




            /*
            |--------------------------------------------------------------------------
            | Transformation en vrai cours Markdown
            |--------------------------------------------------------------------------
            */


            $content = $formatter->format($content);



            if(!$content)
            {

                return back()->with(

                    'error',

                    'Le formatage du cours a échoué.'

                );

            }




            /*
            |--------------------------------------------------------------------------
            | Enregistrement du cours
            |--------------------------------------------------------------------------
            */


            GeneratedCourse::updateOrCreate(

                [

                    'task_id' => $task->getKey()

                ],

                [

                    'title' => $task->getAttribute('title'),

                    'content' => $content

                ]

            );



        }
        catch(\Exception $e)
        {


            return back()->with(

                'error',

                'Erreur récupération cours : '.$e->getMessage()

            );


        }




        return back()->with(

            'success',

            'Cours importé avec succès depuis StudyRaid.'

        );


    }


}