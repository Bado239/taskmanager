<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Symfony\Component\DomCrawler\Crawler;

class CourseSearchService
{

    public function search($subject)
    {

        $query = $this->buildQuery($subject);



        try {


            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->withHeaders([

                    'User-Agent' =>
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'

                ])
                ->get(
                    'https://duckduckgo.com/html/',
                    [
                        'q' => $query
                    ]
                );



        } catch (ConnectionException $e) {


            return [];


        }




        if(!$response->successful())
        {

            return [];

        }




        $crawler = new Crawler(
            $response->body()
        );



        $courses = [];




        if(!$crawler->filter('.result')->count())
        {

            return [];

        }





        $crawler->filter('.result')->each(

            function($node) use (&$courses)

            {


                if(count($courses) >= 10)
                {
                    return;
                }




                if(
                    !$node->filter('.result__a')->count()
                )
                {
                    return;
                }





                $title = trim(

                    $node
                    ->filter('.result__a')
                    ->text()

                );




                $url = $this->cleanUrl(

                    $node
                    ->filter('.result__a')
                    ->attr('href')

                );




                if(!$url)
                {
                    return;
                }





                $host = parse_url(
                    $url,
                    PHP_URL_HOST
                );




                if(!$host)
                {
                    return;
                }





                $score = $this->calculateScore(
                    $title,
                    $url
                );





                $courses[] = [

                    'title' => $title,


                    'source' => str_replace(
                        'www.',
                        '',
                        $host
                    ),



                    'url' => $url,



                    'type' =>
                    'Cours Web',



                    'file_type' =>
                    $this->detectFileType($url),



                    'is_university' =>
                    $this->isUniversity($url),



                    'score' =>
                    $score


                ];



            }

        );






        // supprimer les doublons

        $courses = collect($courses)
            ->unique('url')
            ->values()
            ->toArray();






        // classer par qualité

        usort(

            $courses,

            function($a,$b){

                return $b['score']
                    <=>
                    $a['score'];

            }

        );





        return array_slice(
            $courses,
            0,
            5
        );

    }








    /**
     * Nettoyage URL DuckDuckGo
     */

    private function cleanUrl($url)
    {

        if(!$url)
        {
            return null;
        }



        if(str_contains($url,'uddg='))
        {


            parse_str(

                parse_url(
                    $url,
                    PHP_URL_QUERY
                ),

                $query

            );



            if(isset($query['uddg']))
            {

                return urldecode(
                    $query['uddg']
                );

            }

        }



        return $url;

    }








    /**
     * Détection PDF ou WEB
     */

    private function detectFileType($url)
    {

        return str_contains(
            strtolower($url),
            '.pdf'
        )

        ? 'PDF'

        : 'WEB';

    }








    /**
     * Détection université
     */

    private function isUniversity($url)
    {

        $text = strtolower($url);



        return

            str_contains($text,'univ')

            ||

            str_contains($text,'universite')

            ||

            str_contains($text,'.edu')

            ||

            str_contains($text,'ac.');

    }








    /**
     * Score qualité
     */

    private function calculateScore($title,$url)
    {

        $score = 0;


        $text = strtolower(
            $title.' '.$url
        );



        // PDF
        if(str_contains($text,'pdf'))
        {
            $score += 25;
        }


        // Université
        if(
            str_contains($text,'univ')
            ||
            str_contains($text,'.edu')
            ||
            str_contains($text,'ac.')
        )
        {
            $score += 35;
        }


        // Niveau
        if(
            str_contains($text,'master')
            ||
            str_contains($text,'m1')
        )
        {
            $score += 20;
        }


        // Cours
        if(
            str_contains($text,'cours')
            ||
            str_contains($text,'course')
            ||
            str_contains($text,'lecture')
            ||
            str_contains($text,'polycopi')
        )
        {
            $score += 15;
        }


        // Econométrie spécifique
        if(
            str_contains($text,'logit')
            ||
            str_contains($text,'probit')
            ||
            str_contains($text,'qualitative')
        )
        {
            $score += 15;
        }



        return min($score,100);

    }
        /**
     * Construction intelligente de requête
     */
    private function buildQuery($subject)
    {

        $text = strtolower($subject);


        $expansions = [

            'econometrie des variables qualitatives' =>
            'économétrie qualitative logit probit modèles à choix discret cours master pdf université',


            'econometrie' =>
            'économétrie statistique appliquée regression cours master pdf université',


            'finance' =>
            'finance entreprise finance marché finance internationale cours master 1 pdf université',


            'microeconomie' =>
            'microéconomie théorie consommateur producteur cours master pdf université',


            'macroeconomie' =>
            'macroéconomie croissance inflation politique économique cours master pdf université',


            'statistique' =>
            'statistiques probabilités estimation cours master pdf université'

        ];



        foreach($expansions as $key=>$value)
        {

            if(str_contains($text,$key))
            {
                return $value . " Master 1 cours PDF universitaire";
            }

        }



        return $subject . " Master 1 cours PDF universitaire";

    }


}