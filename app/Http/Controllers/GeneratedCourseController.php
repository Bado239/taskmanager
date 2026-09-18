<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\GeneratedCourse;
use App\Services\StudyRaidService;
use App\Services\CourseFormatterService;
use App\Services\StudyRaidAutoAssignService;


class GeneratedCourseController extends Controller
{


    public function generate(
        Task $task,
        StudyRaidService $studyRaid,
        CourseFormatterService $formatter,
        StudyRaidAutoAssignService $autoAssign
    )
    {

        try {


            /*
            |--------------------------------------------------------------------------
            | Vérifier et créer automatiquement la liaison StudyRaid
            |--------------------------------------------------------------------------
            */


            if(!$task->studyRaidSource)
            {

                $autoAssign->assign($task);

                $task->refresh();

            }



            if(!$task->studyRaidSource)
            {

                return back()->with(

                    'error',

                    'Aucune source StudyRaid trouvée pour ce chapitre.'

                );

            }




            /*
            |--------------------------------------------------------------------------
            | Récupération du cours StudyRaid
            |--------------------------------------------------------------------------
            */


            $content = $studyRaid->getCourse($task);



            if(!$content)
            {

                return back()->with(

                    'error',

                    'Impossible de récupérer le cours StudyRaid.'

                );

            }




            /*
            |--------------------------------------------------------------------------
            | Transformation Markdown professionnelle
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
            | Sauvegarde dans generated_courses
            |--------------------------------------------------------------------------
            */


            GeneratedCourse::updateOrCreate(

                [

                    'task_id'=>$task->id

                ],

                [

                    'title'=>$task->title,

                    'content'=>$content

                ]

            );




        }
        catch(\Exception $e)
        {


            return back()->with(

                'error',

                'Erreur génération cours : '.$e->getMessage()

            );


        }



        return back()->with(

            'success',

            'Cours généré automatiquement avec succès depuis StudyRaid.'

        );


    }


}