<?php

namespace App\Http\Controllers;

use App\Models\CourseResource;
use Illuminate\Http\Request;


class CourseResourceController extends Controller
{


    public function relevance(Request $request, $id)
    {

        $resource = CourseResource::findOrFail($id);


        $resource->update([

            'relevance' => $request->value,

            'saved' => true

        ]);


        return back()->with(
            'success',
            'Cours enregistré dans vos favoris'
        );

    }


}