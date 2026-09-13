<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;


class DictionaryController extends Controller
{


    public function search($word)
    {


        // Nettoyage du mot

        $word = strtolower(trim($word));


        $word = preg_replace(
            '/[^a-zàâçéèêëîïôùûüÿñæœ-]/u',
            '',
            $word
        );



        if(empty($word))
        {
            return response()->json([

                "word" => "",

                "definition" => "Mot invalide."

            ]);
        }



        try {


            // Appel dictionnaire gratuit

            $response = Http::timeout(30)
                ->retry(2,1000)
                ->get(
                    "https://api.dictionaryapi.dev/api/v2/entries/fr/".$word
                );



            if($response->successful())
            {


                $data = $response->json();



                $definition =
                $data[0]['meanings'][0]['definitions'][0]['definition']
                ?? null;



                if($definition)
                {

                    return response()->json([

                        "word"=>$word,

                        "definition"=>$definition

                    ]);

                }


            }



        } catch(\Exception $e) {


            // Si API indisponible

        }




        /*
        |
        | Dictionnaire local de secours
        |
        */


        $local = [


            "moisson" =>
            "Récolte des céréales arrivées à maturité.",


            "moissonneur" =>
            "Personne qui récolte les céréales.",


            "faucille" =>
            "Outil courbé utilisé pour couper les plantes.",


            "prestesse" =>
            "Rapidité et habileté dans l'action.",


            "bienveillance" =>
            "Disposition à vouloir le bien des autres.",


            "serein" =>
            "Calme et tranquille."


        ];



        return response()->json([


            "word"=>$word,


            "definition"=>

            $local[$word]

            ??

            "Définition non disponible. Ce mot nécessite une explication contextuelle."



        ]);



    }


}