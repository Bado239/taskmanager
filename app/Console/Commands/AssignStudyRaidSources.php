<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\StudyRaidSource;

class AssignStudyRaidSources extends Command
{

    protected $signature = 'studyraid:assign';

    protected $description = 'Associe automatiquement les tâches Master aux sources StudyRaid';


    public function handle()
    {

        $tasks = Task::where('type','master')->get();


        foreach($tasks as $task)
        {


            $source = StudyRaidSource::where(
                'title',
                $task->title
            )->first();



            if($source)
            {

                $task->update([

                    'study_raid_source_id'=>$source->id

                ]);


                $this->info(
                    "Associé : ".$task->title
                );

            }


        }


        $this->info(
            "Association terminée."
        );


    }

}