<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CourseSearchService
{

    public function search($subject)
    {

        $query = $this->buildQuery($subject);


        try {

            $response = Http::timeout(30)
                ->retry(2,1000)
                ->withHeaders([

                    'User-Agent' =>
                    'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'

                ])
                ->get(
                    'https://duckduckgo.com/html/',
                    [
                        'q'=>$query
                    ]
                );


            \Log::info(
                "DuckDuckGo status : ".$response->status()
            );

            \Log::info(
                "DuckDuckGo length : ".strlen($response->body())
            );


        } catch (\Exception $e) {


            \Log::error(
                "Recherche erreur : ".$e->getMessage()
            );


            return $this->fallbackSearch($subject);

        }



        if(!$response->successful())
        {
            return $this->fallbackSearch($subject);
        }



        $crawler = new Crawler(
            $response->body()
        );


        $courses = [];



        if(!$crawler->filter('.result')->count())
        {
            return $this->fallbackSearch($subject);
        }




        $crawler->filter('.result')->each(

            function($node) use (&$courses)

            {


                if(count($courses)>=10)
                {
                    return;
                }



                if(!$node->filter('.result__a')->count())
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




                $courses[] = [

                    'title'=>$title,

                    'source'=>str_replace(
                        'www.',
                        '',
                        $host
                    ),

                    'url'=>$url,

                    'type'=>'Cours Web',

                    'file_type'=>$this->detectFileType($url),

                    'is_university'=>$this->isUniversity($url),

                    'score'=>$this->calculateScore(
                        $title,
                        $url
                    )

                ];


            }

        );



        $courses = collect($courses)
            ->unique('url')
            ->values()
            ->toArray();

        // Si moins de 5 résultats, refaire une recherche générale

        if(count($courses) < 5)
        {

            return $this->secondSearch($subject);

        }



        usort(

            $courses,

            function($a,$b){

                return $b['score'] <=> $a['score'];

            }

        );



        if(empty($courses))
        {
            return $this->fallbackSearch($subject);
        }



        return array_slice(
            $courses,
            0,
            5
        );


    }




    private function cleanUrl($url)
    {

        if(!$url)
        {
            return null;
        }


        if(str_starts_with($url,'//'))
        {
            $url='https:'.$url;
        }


        $parts=parse_url($url);


        if(isset($parts['query']))
        {

            parse_str(
                $parts['query'],
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





    private function detectFileType($url)
    {

        return str_contains(
            strtolower($url),
            '.pdf'
        )
        ? 'PDF'
        : 'WEB';

    }





    private function isUniversity($url)
    {

        $text=strtolower($url);


        return
            str_contains($text,'univ')
            ||
            str_contains($text,'.edu')
            ||
            str_contains($text,'ac.');

    }





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


        // Niveau Master
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
        )
        {
            $score += 15;
        }


        // Sénégal / UCAD
        if(
            str_contains($text,'ucad')
            ||
            str_contains($text,'dakar')
            ||
            str_contains($text,'senegal')
            ||
            str_contains($text,'sn')
        )
        {
            $score += 20;
        }


        // PDF direct
        if(str_ends_with($text,'.pdf'))
        {
            $score += 30;
        }


        return min($score,100);

    }




    private function buildQuery($subject)
    {
        return "Cours Master 1 ".$subject." PDF cours université Sénégal";
    }
    private function fallbackSearch($subject)
    {
        return [

            [
                'title' =>
                'Cours Master 1 '.$subject,

                'source' =>
                'Recherche Web',

                'url' =>
                'https://duckduckgo.com/?q='
                .urlencode(
                    'Cours Master 1 '.$subject.' PDF université Sénégal'
                ),

                'type' =>
                'Cours Web',

                'file_type' =>
                'WEB',

                'is_university' =>
                false,

                'score' =>
                50,
            ]

        ];
    }
    private function secondSearch($subject)
    {

        $query = "Cours Master 1 ".$subject." PDF universitaire";


        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent'=>'Mozilla/5.0'
            ])
            ->get(
                'https://duckduckgo.com/html/',
                [
                    'q'=>$query
                ]
            );


        if(!$response->successful())
        {
            return [];
        }


        // ici on réutilise votre logique actuelle
        return $this->extractResults(
            $response->body()
        );

    }


}