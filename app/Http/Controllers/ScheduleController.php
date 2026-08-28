<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScheduleController extends Controller
{


    /**
     * Afficher l'emploi du temps
     */
    public function index()
    {

        $schedule = Schedule::latest()->first();


        return view('schedule.index', compact('schedule'));

    }




    /**
     * Ajouter un emploi du temps
     */
    public function store(Request $request)
    {


        $request->validate([

            'title' => 'required',

            'file' => 'required|file|max:20480|mimes:pdf,jpg,jpeg,png'

        ]);



        $file = $request->file('file');



        // Nom unique du fichier

        $filename =
            time().'_'.$file->getClientOriginalName();



        // Upload dans Supabase Storage

        $path = Storage::disk('s3')
            ->putFileAs(
                'schedules',
                $file,
                $filename,
                'public'
            );



        // URL publique Supabase

        $url =
        'https://zhlojjivmwuuqhzeqpgg.supabase.co/storage/v1/object/public/ebooks/'
        . $path;




        Schedule::create([

            'title' => $request->title,

            'file_path' => $url,

            'type' => strtolower(
                $file->getClientOriginalExtension()
            ),

        ]);



        return back()
            ->with(
                'success',
                'Emploi du temps ajouté avec succès'
            );

    }


}