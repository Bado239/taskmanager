<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class AICourseGeneratorService
{


    public function generate($text, $title)
    {


        // Limitation du document
        $text = mb_substr($text,0,50000);



        $prompt = "

Tu es un professeur universitaire.

Crée un cours complet de niveau Master 1.


Titre du chapitre :

$title


Structure obligatoire :

# Introduction

# Objectifs pédagogiques

# Définitions essentielles

# Développement du cours

# Explications détaillées

# Exemples

# Résumé

# Questions de révision


Document source :

$text

";



        $response = Http::timeout(600)
            ->post(
                'http://localhost:11434/api/generate',
                [

                    'model'=>'qwen2.5:7b',

                    'prompt'=>$prompt,

                    'stream'=>false,

                    'options'=>[
                        'temperature'=>0.3
                    ]

                ]
            );



        if(!$response->successful())
        {

            throw new \Exception(
                $response->body()
            );

        }



        return $response->json('response');


    }


}