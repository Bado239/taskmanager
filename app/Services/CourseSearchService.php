<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Symfony\Component\DomCrawler\Crawler;


class CourseSearchService
{

    public function search($subject)
    {

        $queries = [

            "Master 1 {$subject} cours PDF",

            "{$subject} Master PDF",

            "{$subject} cours universitaire",

            "{$subject} syllabus Master",

            "{$subject} lecture notes Master PDF"

        ];


        $allCourses = [];


        foreach($queries as $query)
        {

            $results = $this->duckDuckGoSearch($query);


            $allCourses = array_merge(
                $allCourses,
                $results
            );

        }



        if(count($allCourses) == 0)
        {

            return $this->defaultCourses($subject);

        }




        return collect($allCourses)

            ->unique('url')

            ->filter(function($course){

                // On garde uniquement les résultats pertinents

                return $course['score'] >= 20;

            })

            ->sortByDesc('score')

            ->take(5)

            ->values()

            ->toArray();


    }







        private function duckDuckGoSearch($query)
        {

            try {

                $response = Http::timeout(10)
                    ->retry(2,500)
                    ->withHeaders([
                        'User-Agent'=>'Mozilla/5.0'
                    ])
                    ->get(
                        'https://duckduckgo.com/html/',
                        [
                            'q'=>$query
                        ]
                    );

            }

            catch(ConnectionException $e)
            {
                return [];
            }


            if(!$response->successful())
            {
                return [];
            }



            $crawler = new Crawler($response->body());


            $courses=[];



            $crawler->filter('.result')->each(function($node) use (&$courses)
            {


                if(count($courses)>=15)
                {
                    return;
                }



                if(!$node->filter('a.result__a')->count())
                {
                    return;
                }



                $title = trim(
                    $node
                    ->filter('a.result__a')
                    ->text()
                );



                $link = $node
                    ->filter('a.result__a')
                    ->attr('href');



                $url = $this->cleanUrl($link);



                if(!$url)
                {
                    return;
                }



                $host=parse_url($url,PHP_URL_HOST);



                if(!$host)
                {
                    return;
                }



                $courses[]=[


                    'title'=>$title,


                    'source'=>str_replace(
                        'www.',
                        '',
                        $host
                    ),


                    'url'=>$url,


                    'type'=>'Cours Web',


                    'file_type'=>$this->detectFileType($url),


                    'is_university'=>
                        $this->isUniversity($url),


                    'score'=>
                        $this->calculateScore(
                            $title,
                            $url
                        )


                ];


            });



            return $courses;

        }







    private function defaultCourses($subject)
    {

        return [

            [

                'title'=>"Aucun cours trouvé pour {$subject}",

                'source'=>"Recherche Web",

                'url'=>"#",

                'type'=>"Recherche",

                'file_type'=>"WEB",

                'is_university'=>false,

                'score'=>0

            ]

        ];

    }








    private function cleanUrl($url)
    {

        if(!$url)
        {
            return null;
        }


        if(str_contains($url,'uddg='))
        {

            parse_str(
                parse_url($url,PHP_URL_QUERY),
                $query
            );


            if(isset($query['uddg']))
            {
                return urldecode($query['uddg']);
            }

        }


        if(str_starts_with($url,'//'))
        {
            return 'https:'.$url;
        }


        return $url;

    }







    private function detectFileType($url)
    {


        if(str_contains(
            strtolower($url),
            '.pdf'
        ))
        {

            return "PDF";

        }


        return "WEB";


    }









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









    private function calculateScore($title,$url)
    {


        $score = 0;


        $text = strtolower(
            $title.' '.$url
        );




        // PDF
        if(
            str_contains($text,'pdf')
            ||
            str_contains($text,'.pdf')
        )
        {

            $score += 40;

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

            $score +=20;

        }




        // Cours

        if(
            str_contains($text,'cours')
            ||
            str_contains($text,'course')
            ||
            str_contains($text,'syllabus')
            ||
            str_contains($text,'lecture')
        )
        {

            $score +=20;

        }



        return $score;


    }


}