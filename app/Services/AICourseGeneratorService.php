<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AICourseGeneratorService
{

    public function generate($text,$title)
    {

        $response = Http::withToken(
            config('services.openai.key')
        )
        ->timeout(120)
        ->post(
            'https://api.openai.com/v1/chat/completions',
            [

                'model'=>'gpt-4o-mini',

                'messages'=>[

                    [
                        'role'=>'system',
                        'content'=>
                        "Tu es un professeur universitaire.
                        Transforme les documents fournis en un cours complet, structuré et pédagogique."
                    ],


                    [
                        'role'=>'user',
                        'content'=>

                        "Matière : ".$title."

                        À partir du document ci-dessous, crée un cours complet avec :

                        - Introduction
                        - Objectifs pédagogiques
                        - Définitions importantes
                        - Développement détaillé des parties
                        - Exemples
                        - Points clés à retenir
                        - Résumé final
                        - Questions de révision


                        DOCUMENT :

                        ".$text

                    ]

                ],

                'temperature'=>0.4

            ]
        );


        if(!$response->successful())
        {
            throw new \Exception(
                $response->body()
            );
        }


        return $response
            ->json('choices.0.message.content');


    }


}