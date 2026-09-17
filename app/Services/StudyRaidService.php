<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;


class StudyRaidService
{


    public function getCourse($title)
    {


        $courses = [

            "Généralités sur les finances publiques" =>
            "https://app.studyraid.com/fr/read/122635/5696968/quest-que-les-finances-publiques",

        ];



        if (!isset($courses[$title])) {
            return null;
        }



        $response = Http::timeout(30)
            ->get($courses[$title]);



        if (!$response->successful()) {
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
        | Extraction DOM
        |--------------------------------------------------------------------------
        */


        libxml_use_internal_errors(true);


        $dom = new DOMDocument();

        $dom->loadHTML(
            '<?xml encoding="UTF-8">'.$html
        );


        $xpath = new DOMXPath($dom);



        /*
        |--------------------------------------------------------------------------
        | Chercher le vrai contenu article
        |--------------------------------------------------------------------------
        */


        $content = '';



        $articles = $xpath->query(
            "//article"
        );



        if($articles->length > 0)
        {

            foreach($articles as $article)
            {
                $content .= $dom->saveHTML($article);
            }

        }
        else
        {

            $main = $xpath->query("//main");


            if($main->length > 0)
            {
                foreach($main as $node)
                {
                    $content .= $dom->saveHTML($node);
                }
            }

        }




        if(empty($content))
        {
            return null;
        }




        /*
        |--------------------------------------------------------------------------
        | Garder les balises utiles
        |--------------------------------------------------------------------------
        */


        $content = preg_replace(
            '/<(script|style).*?<\/\1>/is',
            '',
            $content
        );



        // titres

        $content = preg_replace(
            '/<h2[^>]*>(.*?)<\/h2>/is',
            "\n\n## $1\n\n",
            $content
        );


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
        | Nettoyage StudyRaid
        |--------------------------------------------------------------------------
        */


        $remove = [

            "Finances Publiques au Sénégal : Fondamentaux et Cadre Légal",

            "10 sections",

            "41 chapitres",

            "0/4",

            "open navigation menu",

            "Créer un cours avec l'IA",

            "Poser une question",

            "Créez votre premier cours",

            "Commencer",

            "Quiz",

            "Résumé",

            "Examen",

            "Cartes mémoire",

            "Jeu",

            "Générer avec l'IA",

            "ProAudio",

            "Vidéo",

            "Illustration",

            "Certification",

            "Sur cette page",

            "Chapitre Suivant",

            "Dernière mise à jour",

            "Qu'est-ce que les finances publiques ?",

            "\# Définition et Périmètre des Finances Publiques",

            "Summary",

            "Exam",

            "Flashcards",

            "Game",

            "Generate with AI",

            "Video",

            "On this page",

            "Next Chapter",

            "Distinction finances publiques et finances privées",

            "Last Updated",

        ];



        foreach($remove as $word)
        {

            $text = str_replace(
                $word,
                '',
                $text
            );

        }




        /*
        |--------------------------------------------------------------------------
        | Correction Markdown
        |--------------------------------------------------------------------------
        */


        $text = str_replace(
            '\#',
            '#',
            $text
        );



        // supprimer compteurs 0/4

        $text = preg_replace(
            '/\d+\/\d+/',
            '',
            $text
        );



        // Retours propres

        $text = preg_replace(
            "/[ \t]+/",
            " ",
            $text
        );



        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );

        // Correction Markdown des titres

        $text = str_replace(
            '\#',
            '',
            $text
        );


        // Supprimer les doubles titres
        $text = str_replace(
            '# ##',
            '##',
            $text
        );


        // Nettoyer les titres seuls
        $text = preg_replace(
            '/\s+##\s+/',
            "\n\n## ",
            $text
        );

        // Ajouter espace après les titres

        $text = preg_replace(
            '/(## [^\n]+)([A-ZÉÈÀÂÎÔÛ])/u',
            "$1\n\n$2",
            $text
        );

        // Ajouter des espaces après les titres

        $text = preg_replace(
            '/(## [^\n]+)([A-ZÉÈÀÂÎÔÛ])/u',
            "$1\n\n$2",
            $text
        );


        // Corriger Note

        $text = str_replace(
            '# ## Note',
            '> ### Note',
            $text
        );


        // Supprimer les éléments restants

        $remove = [

            '- Définition générale',
            '- Le périmètre concerné',
            '- Les opérations financières',
            '- Les fonctions financières',
            '- Les principes fondamentaux',

            '✨Create your course with AIfor free on any topic',

            'on /2026',

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
        | Mise en forme finale du cours
        |--------------------------------------------------------------------------
        */


        // Supprimer les antislash devant Markdown

        $text = str_replace(
            ['\#','\\'],
            '',
            $text
        );



        // Supprimer le grand titre StudyRaid

        $text = str_replace(
            'Définition et Périmètre des Finances Publiques',
            '',
            $text
        );



        // Corriger les titres collés au texte

        $text = preg_replace(
            '/(## [^\n]+)\s*/',
            "$1\n\n",
            $text
        );



        // Ajouter des sauts avant les sections

        $sections = [

            '## Définition générale',

            '## Le périmètre concerné',

            '## Les opérations financières',

            '## Les fonctions financières',

            '## Les principes fondamentaux',

        ];


        foreach($sections as $section)
        {

            $text = str_replace(
                $section,
                "\n\n".$section."\n\n",
                $text
            );

        }



        // Corriger le bloc Note

        $text = str_replace(
            '### Note',
            "\n\n> ### Note\n\n",
            $text
        );



        // Nettoyage final

        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );



        return trim($text);


    }


}