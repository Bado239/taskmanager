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
            "https://app.studyraid.com/fr/read/122635/5696968/quest-ce-que-les-finances-publiques",

        ];



        if (!isset($courses[$title])) {

            return null;

        }



        $url = $courses[$title];



        $response = Http::timeout(30)->get($url);



        if (!$response->successful()) {

            return null;

        }



        $html = $response->body();



        /*
        |--------------------------------------------------------------------------
        | Suppression scripts et styles
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


        libxml_use_internal_errors(true);


        $dom = new DOMDocument();


        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $html
        );



        $xpath = new DOMXPath($dom);



        $content = '';



        /*
        | Recherche des zones principales
        */

        $nodes = $xpath->query(
            "//article | //main | 
             //*[@class='prose'] |
             //*[@class='content']"
        );



        if ($nodes->length > 0) {


            foreach ($nodes as $node) {

                $content .= $node->textContent;

            }


        } 
        else {


            // Solution de secours

            $content = $dom->textContent;

        }





        /*
        |--------------------------------------------------------------------------
        | Nettoyage texte
        |--------------------------------------------------------------------------
        */


        $text = html_entity_decode(
            $content,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );



        /*
        | Supprimer éléments inutiles StudyRaid
        */


        $remove = [

            'open navigation menu',

            'Créer un cours avec l\'IA',

            'Poser une question',

            'Créez votre premier cours',

            'Commencer',

            'Quiz',

            'Résumé',

            'Examen',

            'Cartes mémoire',

            'Jeu',

            'Générer avec l\'IA',

            'ProAudio',

            'Vidéo',

            'Illustration',

            'Certification',

            'Chapitre Suivant',

            'Dernière mise à jour',

        ];



        foreach ($remove as $item) {


            $text = str_replace(
                $item,
                '',
                $text
            );


        }




        /*
        |--------------------------------------------------------------------------
        | Nettoyage intelligent du cours
        |--------------------------------------------------------------------------
        */


        // Décodage HTML
        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );


        // Supprimer les éléments StudyRaid

        $remove = [

            'Finances Publiques au Sénégal : Fondamentaux et Cadre Légal',
            '10 sections',
            '41 chapitres',
            '0/4',

            'open navigation menu',
            'Créer un cours avec l\'IA',
            'Poser une question',
            'Créez votre premier cours',
            'Commencer',

            'Quiz',
            'Résumé',
            'Examen',
            'Cartes mémoire',
            'Jeu',

            'Générer avec l\'IA',
            'ProAudio',
            'Vidéo',
            'Illustration',

            'Certification',

            'Sur cette page',

            'Dernière mise à jour',
        ];


        foreach($remove as $item)
        {
            $text = str_replace(
                $item,
                '',
                $text
            );
        }



        // Supprimer les compteurs StudyRaid

        $text = preg_replace(
            '/\d+\/\d+/',
            '',
            $text
        );



        // Corriger les titres Markdown

        $text = str_replace(
            '\#',
            '#',
            $text
        );



        // Ajouter des retours avant les titres

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



        // Ajouter retours après les titres

        $text = preg_replace(
            '/(## [^\n]+)\s+/',
            "$1\n\n",
            $text
        );



        // Nettoyage espaces

        $text = preg_replace(
            "/[ \t]+/",
            " ",
            $text
        );


        $text = preg_replace(
            "/\n\s*\n\s*\n+/",
            "\n\n",
            $text
        );


        return trim($text);

        $text = preg_replace(
            '/\n\s*\n\s*\n+/',
            "\n\n",
            $text
        );



        /*
        |--------------------------------------------------------------------------
        | Ajouter des retours avant les titres
        |--------------------------------------------------------------------------
        */


        $titles = [

            'Définition générale',

            'Le périmètre concerné',

            'Les opérations financières',

            'Les fonctions financières',

            'Les principes fondamentaux',

        ];



        foreach ($titles as $section) {


            $text = str_replace(
                $section,
                "\n\n## ".$section."\n\n",
                $text
            );


        }



        return trim($text);


    }


}