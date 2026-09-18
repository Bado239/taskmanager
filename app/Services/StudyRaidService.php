<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Task;


class StudyRaidService
{

    public function getCourse(Task $task)
    {


        /*
        |--------------------------------------------------------------------------
        | Récupération automatique de la source StudyRaid
        |--------------------------------------------------------------------------
        */


        $source = $task->studyRaidSource;


        if(!$source)
        {
            return null;
        }



        $url = $source->url;



        /*
        |--------------------------------------------------------------------------
        | Chargement de la page StudyRaid
        |--------------------------------------------------------------------------
        */


        $response = Http::timeout(30)
            ->get($url);



        if(!$response->successful())
        {
            return null;
        }



        $html = $response->body();




        /*
        |--------------------------------------------------------------------------
        | Nettoyage HTML
        |--------------------------------------------------------------------------
        */


        $html = preg_replace(
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '',
            $html
        );


        $html = preg_replace(
            '/<style\b[^>]*>(.*?)<\/style>/is',
            '',
            $html
        );




        /*
        |--------------------------------------------------------------------------
        | Extraction du contenu principal
        |--------------------------------------------------------------------------
        */


        preg_match(
            '/<article.*?>(.*?)<\/article>/is',
            $html,
            $match
        );


        if(isset($match[1]))
        {
            $content = $match[1];
        }
        else
        {
            $content = $html;
        }




        /*
        |--------------------------------------------------------------------------
        | Conversion HTML vers Markdown
        |--------------------------------------------------------------------------
        */


        // titres niveau 2

        $content = preg_replace(
            '/<h2[^>]*>(.*?)<\/h2>/is',
            "\n\n## $1\n\n",
            $content
        );



        // titres niveau 3

        $content = preg_replace(
            '/<h3[^>]*>(.*?)<\/h3>/is',
            "\n\n### $1\n\n",
            $content
        );



        // paragraphes

        $content = preg_replace(
            '/<p[^>]*>(.*?)<\/p>/is',
            "$1\n\n",
            $content
        );



        // listes

        $content = preg_replace(
            '/<li[^>]*>(.*?)<\/li>/is',
            "- $1\n",
            $content
        );




        /*
        |--------------------------------------------------------------------------
        | Suppression HTML
        |--------------------------------------------------------------------------
        */


        $text = strip_tags($content);



        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );





        /*
        |--------------------------------------------------------------------------
        | Suppression éléments StudyRaid
        |--------------------------------------------------------------------------
        */


        $remove = [


            "Finances Publiques au Sénégal : Fondamentaux et Cadre Légal",

            "10 sections",

            "41 chapitres",


            "Summary",

            "Exam",

            "Flashcards",

            "Game",

            "Generate with AI",

            "Video",

            "On this page",

            "Next Chapter",

            "Last Updated",


            "Créer un cours avec l'IA",

            "Poser une question",

            "Générer avec l'IA",


            "Certification",

        ];



        foreach($remove as $item)
        {

            $text = str_replace(
                $item,
                '',
                $text
            );

        }





        /*
        |--------------------------------------------------------------------------
        | Nettoyage Markdown final
        |--------------------------------------------------------------------------
        */


        $text = str_replace(
            '\#',
            '#',
            $text
        );



        // supprimer espaces inutiles

        $text = preg_replace(
            "/[ \t]+/",
            " ",
            $text
        );



        // conserver les paragraphes

        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );



        return trim($text);

    }

}