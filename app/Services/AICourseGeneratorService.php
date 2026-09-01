<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class AICourseGeneratorService
{


    public function generate($text,$title)
    {


        $text = mb_substr($text,0,50000);



        $prompt = "

Tu es un professeur universitaire.

Rédige un cours complet de niveau Master 1.


Titre du chapitre :

$title


Structure obligatoire :


# Introduction

# Objectifs pédagogiques

# Définitions essentielles

# Développement détaillé du cours

# Exemples pratiques

# Résumé

# Questions de révision


Documents sources :


$text


";



        $response = Http::timeout(300)

        ->withHeaders([
            'Content-Type'=>'application/json',
        ])

        ->post(

        'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key='
        .config('services.gemini.key'),


        [

            'contents'=>[

                [

                    'parts'=>[

                        [

                            'text'=>$prompt

                        ]

                    ]

                ]

            ],

            'generationConfig'=>[

                'temperature'=>0.4,

                'maxOutputTokens'=>8000

            ]

        ]);




        if(!$response->successful())
        {

            throw new \Exception(
                $response->body()
            );

        }



        return $response
        ->json('candidates.0.content.parts.0.text');


    }


}