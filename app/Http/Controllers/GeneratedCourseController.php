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


            // Recherche du cours depuis StudyRaid
            $content = $studyRaid->getCourse($task);


            $content = $formatter->format($content);



            if(!$content)
            {

                return back()->with(

                    'error',

                    'Aucun cours trouvé pour ce chapitre.'

                );

            }



            // Enregistrement du cours dans la base

            GeneratedCourse::updateOrCreate(

                [

                    'task_id' => $task->id

                ],

                [

                    'title' => $task->title,

                    'content' => $content

                ]

            );




        } catch(\Exception $e) {


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