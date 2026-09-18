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

        $this->info("Début de l'association StudyRaid...");


        $tasks = Task::where('type','master')->get();


        $count = 0;


        foreach($tasks as $task)
        {


            if($task->study_raid_source_id)
            {

                $this->line(
                    "Déjà associé : ".$task->title
                );

                continue;

            }



            $source = StudyRaidSource::whereRaw(
                    'LOWER(title) LIKE ?',
                    [
                        '%'.mb_strtolower($task->title).'%'
                    ]
                )

                ->where(function($query) use ($task){

                    $query
                        ->where('subject',$task->project_name)
                        ->orWhereNull('subject');

                })

                ->first();



            if(!$source)
            {

                $this->warn(
                    "Aucune source trouvée : ".$task->title
                );

                continue;

            }



            $task->update([

                'study_raid_source_id'=>$source->id

            ]);



            $source->update([

                'task_id'=>$task->id

            ]);



            $count++;


            $this->info(
                "Associé : ".$task->title
            );

        }



        $this->info(
            "Association terminée. Total : ".$count
        );


        return Command::SUCCESS;

    }

}