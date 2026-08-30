<?php

namespace App\Http\Controllers;

use App\Models\CourseResource;
use Illuminate\Http\Request;

class CourseResourceController extends Controller
{

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