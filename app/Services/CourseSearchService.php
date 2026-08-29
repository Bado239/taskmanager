<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CourseSearchService
{

    public function search($subject)
    {

        $query = "cours Master 1 {$subject} PDF universitaire";


        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0'
        ])
        ->get('https://duckduckgo.com/html/', [
            'q' => $query
        ]);



        if (!$response->successful()) {

            return [];

        }



        $crawler = new Crawler($response->body());


        $courses = [];



        $crawler->filter('.result')->each(function ($node) use (&$courses) {


            if (count($courses) >= 10) {
                return;
            }



            if (
                !$node->filter('.result__title')->count()
                ||
                !$node->filter('.result__a')->count()
            ) {

                return;

            }




            $title = trim(
                $node
                ->filter('.result__title')
                ->text()
            );




            $url = $this->cleanUrl(

                $node
                ->filter('.result__a')
                ->attr('href')

            );



            $host = parse_url($url, PHP_URL_HOST);



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


                'type' => 'Cours Web',



                'file_type' => str_contains(
                    strtolower($url),
                    '.pdf'
                )
                ? 'PDF'
                : 'WEB',



                'is_university' =>
                    str_contains(
                        strtolower($url),
                        'univ'
                    )
                    ||
                    str_contains(
                        strtolower($url),
                        '.edu'
                    )
                    ||
                    str_contains(
                        strtolower($url),
                        'universite'
                    ),



                'score' => $score

            ];



        });





        // Classement du meilleur au moins bon

        usort($courses, function($a,$b){

            return $b['score'] <=> $a['score'];

        });



        // Garder uniquement les 5 meilleurs

        return array_slice($courses,0,5);


    }





    /**
     * Nettoyer les liens DuckDuckGo
     */
    private function cleanUrl($url)
    {

        if(str_contains($url,'uddg='))
        {

            parse_str(
                parse_url($url, PHP_URL_QUERY),
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
     * Calcul qualité résultat
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
            $score += 30;
        }



        // Université
        if(
            str_contains($text,'univ')
            ||
            str_contains($text,'universite')
            ||
            str_contains($text,'.edu')
        )
        {
            $score += 30;
        }



        // Niveau Master
        if(str_contains($text,'master'))
        {
            $score += 15;
        }



        // Cours
        if(
            str_contains($text,'cours')
            ||
            str_contains($text,'course')
        )
        {
            $score += 15;
        }



        return $score;

    }


}