<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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


        // Suppression des scripts et styles
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);


        // Conversion HTML vers texte
        $text = strip_tags($html);

        
        // Suppression des éléments inutiles StudyRaid

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
        ];


        foreach($remove as $item)
        {
            $text = str_replace($item, '', $text);
        }


        // Nettoyage espaces

        $text = preg_replace('/\s+/', ' ', $text);

        // Décodage des caractères HTML
        $text = html_entity_decode($text);


        // Nettoyage espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);


        return trim($text);

    }
}