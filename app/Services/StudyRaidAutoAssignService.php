<?php

namespace App\Services;


use App\Models\Task;
use App\Models\StudyRaidSource;



class StudyRaidAutoAssignService
{


    public function assign(Task $task)
    {


        /*
        |--------------------------------------------------------------------------
        | Vérifier que c'est une tâche Master
        |--------------------------------------------------------------------------
        */


        if($task->type !== 'master')
        {
            return false;
        }




        /*
        |--------------------------------------------------------------------------
        | Recherche de la source StudyRaid
        |--------------------------------------------------------------------------
        */


        $source = StudyRaidSource::findByTitle(
            $task->title
        );




        if(!$source)
        {
            return false;
        }





        /*
        |--------------------------------------------------------------------------
        | Association source <-> tâche
        |--------------------------------------------------------------------------
        */


        $source->update([

            'task_id'=>$task->id

        ]);




        $task->update([

            'study_raid_source_id'=>$source->id

        ]);





        return true;


    }


}