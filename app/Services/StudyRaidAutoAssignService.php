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
        | Vérifier si déjà associé
        |--------------------------------------------------------------------------
        */


        if($task->studyRaidSource)
        {
            return true;
        }




        /*
        |--------------------------------------------------------------------------
        | Matière
        |--------------------------------------------------------------------------
        */


        $subject = trim($task->project_name);




        /*
        |--------------------------------------------------------------------------
        | Niveau par défaut
        |--------------------------------------------------------------------------
        */


        $level = "Master 1";





        /*
        |--------------------------------------------------------------------------
        | Normalisation du titre
        |--------------------------------------------------------------------------
        */


        $title = trim(
            preg_replace(
                '/\s+/',
                ' ',
                $task->title
            )
        );






        /*
        |--------------------------------------------------------------------------
        | Recherche de la source StudyRaid
        |--------------------------------------------------------------------------
        */


        $source = StudyRaidSource::where(
                'active',
                true
            )

            ->whereRaw(
                'LOWER(title) LIKE ?',
                [
                    '%'.mb_strtolower($title).'%'
                ]
            )

            ->where(function($query) use ($subject){

                $query
                    ->where('subject',$subject)
                    ->orWhereNull('subject');

            })

            ->where(function($query) use ($level){

                $query
                    ->where('level',$level)
                    ->orWhereNull('level');

            })

            ->first();






        /*
        |--------------------------------------------------------------------------
        | Aucune source trouvée
        |--------------------------------------------------------------------------
        */


        if(!$source)
        {

            return false;

        }






        /*
        |--------------------------------------------------------------------------
        | Association source → tâche
        |--------------------------------------------------------------------------
        */


        $source->update([

            'task_id'=>$task->id

        ]);






        /*
        |--------------------------------------------------------------------------
        | Association tâche → source
        |--------------------------------------------------------------------------
        */


        $task->update([

            'study_raid_source_id'=>$source->id

        ]);






        return true;


    }



}