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
        | Récupération automatique source StudyRaid
        |--------------------------------------------------------------------------
        */


        $source = $task->studyRaidSource;


        if(!$source || !$source->active)
        {
            return null;
        }



        $url = $source->url;



        /*
        |--------------------------------------------------------------------------
        | Chargement StudyRaid
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
        | Extraction contenu principal
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


        // Titres H2

        $content = preg_replace(
            '/<h2[^>]*>(.*?)<\/h2>/is',
            "\n\n## $1\n\n",
            $content
        );



        // Titres H3

        $content = preg_replace(
            '/<h3[^>]*>(.*?)<\/h3>/is',
            "\n\n### $1\n\n",
            $content
        );



        // Paragraphes

        $content = preg_replace(
            '/<p[^>]*>(.*?)<\/p>/is',
            "$1\n\n",
            $content
        );



        // Listes

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


            // Titres StudyRaid

            "Finances Publiques au Sénégal : Fondamentaux et Cadre Légal",

            "Définition et Périmètre des Finances Publiques",

            "Qu'est-ce que les finances publiques ?",



            // Menus

            "Summary",

            "Exam",

            "Flashcards",

            "Game",

            "Generate with AI",

            "Video",

            "ProAudio",

            "Illustration",

            "Quiz",

            "Résumé",

            "Examen",

            "Cartes mémoire",

            "Jeu",


            // IA StudyRaid

            "Créer un cours avec l'IA",

            "Poser une question",

            "Générer avec l'IA",

            "Create your course with AI for free on any topic",



            // Navigation

            "On this page",

            "Sur cette page",

            "Next Chapter",

            "Chapitre Suivant",

            "Last Updated",

            "Dernière mise à jour",


            // Certification

            "Certification",


            // Autres

            "Distinction finances publiques et finances privées",

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
        | Suppression automatique des parasites
        |--------------------------------------------------------------------------
        */


        // Supprime les compteurs 0/4, 1/10...

        $text = preg_replace(
            '/\d+\/\d+/',
            '',
            $text
        );



        // Supprime les dates StudyRaid

        $text = preg_replace(
            '/on\s+\d{1,2}\/\d{1,2}\/\d{4}/',
            '',
            $text
        );



        // Supprime lignes de sommaire

        $text = preg_replace(
            '/-\s*(Définition générale|Le périmètre concerné|Les opérations financières|Les fonctions financières|Les principes fondamentaux)/',
            '',
            $text
        );





        /*
        |--------------------------------------------------------------------------
        | Nettoyage Markdown
        |--------------------------------------------------------------------------
        */


        // Corrige les #

        $text = str_replace(
            '\#',
            '#',
            $text
        );



        // Supprime espaces multiples

        $text = preg_replace(
            "/[ \t]+/",
            " ",
            $text
        );



        // Sépare correctement les titres

        $text = preg_replace(
            '/(## [^\n]+)\s*/',
            "$1\n\n",
            $text
        );



        // Sépare les paragraphes

        $text = preg_replace(
            '/([.!?])\s+(## )/',
            "$1\n\n$2",
            $text
        );



        // Listes propres

        $text = str_replace(
            " - ",
            "\n- ",
            $text
        );



        // Note Markdown

        $text = str_replace(
            "### Note",
            "\n\n> ### Note\n\n",
            $text
        );



        // Nettoyage lignes vides

        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );



        return trim($text);


    }

}