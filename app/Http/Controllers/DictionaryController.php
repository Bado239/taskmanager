<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;


class DictionaryController extends Controller
{


    public function search($word)
    {


        // Nettoyage

        $word = strtolower(trim($word));


        $word = preg_replace(
            '/[^a-zàâçéèêëîïôùûüÿñæœ-]/u',
            '',
            $word
        );



        if(!$word)
        {
            return response()->json([
                "definition"=>"Mot invalide"
            ]);
        }



        // Appel dictionnaire gratuit

        $response = Http::timeout(10)
            ->get(
                "https://api.dictionaryapi.dev/api/v2/entries/fr/".$word
            );



        if($response->successful())
        {


            $data = $response->json();



            return response()->json([

                "word"=>$word,


                "definition" =>
                $data[0]['meanings'][0]['definitions'][0]['definition']
                ?? 
                "Définition non disponible."

            ]);


        }



        // Si le dictionnaire ne connait pas le mot

        return response()->json([

            "word"=>$word,

            "definition"=>
            "Ce mot est absent du dictionnaire. Une explication contextuelle sera proposée."

        ]);



    }


}