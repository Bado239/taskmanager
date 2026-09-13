<?php

namespace App\Http\Controllers;

use App\Models\CourseResource;
use App\Services\CourseSearchService;
use Illuminate\Http\Request;

class CourseResourceController extends Controller
{

    protected CourseSearchService $searchService;


    public function __construct(
        CourseSearchService $searchService
    )
    {
        $this->searchService = $searchService;
    }



    /**
     * Recherche automatique des cours
     */
    public function searchCourses($taskId)
    {

        $task = \App\Models\Task::findOrFail($taskId);



        /*
        Matière recherchée
        */
        $subject = $task->subject
            ?? $task->title
            ?? '';



        /*
        Construction requête intelligente
        */
        $query = $subject . ' Master 1 cours PDF universitaire';



        /*
        Appel moteur DuckDuckGo
        */
        $results = $this->searchService
            ->search($subject);



        /*
        Suppression anciens résultats
        */
        CourseResource::where(
            'task_id',
            $task->id
        )->delete();



        /*
        Enregistrement nouveaux cours
        */
        foreach($results as $course)
        {

            CourseResource::create([

                'task_id' => $task->id,

                'title' =>
                    $course['title'],

                'url' =>
                    $course['url'],

                'source' =>
                    $course['source'],

                'type' =>
                    $course['type'],

                'file_type' =>
                    $course['file_type'],

                'is_university' =>
                    $course['is_university'],

                'score' =>
                    $course['score'],

                'saved' =>
                    false,

            ]);

        }



        return redirect()
            ->route(
                'tasks.learning',
                $task->id
            )
            ->with(
                'success',
                'Recherche terminée'
            );

    }




    /**
     * Noter la pertinence d'un cours
     */
    public function rate(Request $request, $id)
    {

        $resource = CourseResource::findOrFail($id);


        $resource->update([

            'rating' => (int) $request->rating

        ]);


        return back()->with(
            'success',
            'Évaluation enregistrée'
        );

    }





    /**
     * Sauvegarder un cours
     */
    public function save($id)
    {

        $resource = CourseResource::findOrFail($id);


        $resource->update([

            'saved' => true

        ]);


        return back()->with(
            'success',
            'Cours ajouté aux favoris'
        );

    }





    /**
     * Ajouter une note personnelle
     */
    public function note(Request $request, $id)
    {

        $resource = CourseResource::findOrFail($id);


        $resource->update([

            'notes' => $request->notes

        ]);


        return back()->with(
            'success',
            'Note sauvegardée'
        );

    }

}