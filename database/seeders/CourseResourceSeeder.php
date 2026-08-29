<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseResource;
use App\Models\Task;

class CourseResourceSeeder extends Seeder
{
    public function run(): void
    {

        // Recherche du chapitre Finance
        $task = Task::where('title', 'Apprendre')
                    ->first();


        if (!$task) {
            return;
        }



        CourseResource::create([
            'task_id' => $task->id,
            'title' => 'Cours de Finance d’entreprise - Master 1',
            'source' => 'Université Cheikh Anta Diop (UCAD) Sénégal',
            'url' => 'https://www.ucad.sn',
            'type' => 'Cours',
            'order' => 1,
        ]);



        CourseResource::create([
            'task_id' => $task->id,
            'title' => 'Supports de Finance et Gestion Financière',
            'source' => 'Université Gaston Berger (UGB) Sénégal',
            'url' => 'https://www.ugb.sn',
            'type' => 'Cours',
            'order' => 2,
        ]);



        CourseResource::create([
            'task_id' => $task->id,
            'title' => 'Corporate Finance - OpenCourseWare',
            'source' => 'MIT OpenCourseWare',
            'url' => 'https://ocw.mit.edu',
            'type' => 'Cours',
            'order' => 3,
        ]);



        CourseResource::create([
            'task_id' => $task->id,
            'title' => 'Financial Management',
            'source' => 'Harvard Online Learning',
            'url' => 'https://pll.harvard.edu',
            'type' => 'Cours',
            'order' => 4,
        ]);



        CourseResource::create([
            'task_id' => $task->id,
            'title' => 'Introduction to Finance',
            'source' => 'Yale Open Courses',
            'url' => 'https://oyc.yale.edu',
            'type' => 'Cours',
            'order' => 5,
        ]);

    }
}