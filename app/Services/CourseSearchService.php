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

            "Master 1 {$subject} cours PDF université",

            "{$subject} Master course syllabus PDF",

            "{$subject} filetype:pdf cours universitaire"

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



        // Si aucun résultat web
        if(count($allCourses) == 0)
        {

            $allCourses = $this->defaultCourses($subject);

        }



        return collect($allCourses)

            ->unique('url')

            ->sortByDesc('score')

            ->take(5)

            ->values()

            ->toArray();


    }







    /**
     * Recherche DuckDuckGo
     */
    private function duckDuckGoSearch($query)
    {


        try {


            $response = Http::timeout(8)

                ->retry(1,500)

                ->withHeaders([

                    'User-Agent'=>
                    'Mozilla/5.0 Chrome/120'

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





        $crawler = new Crawler(

            $response->body()

        );




        $courses=[];



        if(!$crawler->filter('.result')->count())

        {

            return [];

        }




        $crawler->filter('.result')->each(

            function($node) use (&$courses)

            {


                if(count($courses)>=10)

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





                $courses[]=[


                    'title'=>$title,


                    'source'=>str_replace(

                        'www.',

                        '',

                        parse_url(
                            $url,
                            PHP_URL_HOST
                        )

                    ),


                    'url'=>$url,


                    'type'=>'Cours Web',


                    'file_type'=>
                    $this->detectFileType($url),


                    'is_university'=>
                    $this->isUniversity($url),


                    'score'=>
                    $this->calculateScore(
                        $title,
                        $url
                    )

                ];



            }

        );



        return $courses;



    }









    /**
     * Résultats secours
     */
    private function defaultCourses($subject)
    {


        return [


            [

            'title'=>"Master 1 {$subject} - Support universitaire",

            'source'=>"Université Paris-Saclay",

            'url'=>"https://www.universite-paris-saclay.fr",

            'type'=>"Cours Web",

            'file_type'=>"WEB",

            'is_university'=>true,

            'score'=>80

            ],




            [

            'title'=>"Cours {$subject} Master",

            'source'=>"Université de Rennes",

            'url'=>"https://www.univ-rennes.fr",

            'type'=>"Cours Web",

            'file_type'=>"WEB",

            'is_university'=>true,

            'score'=>75

            ],




            [

            'title'=>"OpenCourseWare {$subject}",

            'source'=>"MIT OpenCourseWare",

            'url'=>"https://ocw.mit.edu",

            'type'=>"Cours Web",

            'file_type'=>"WEB",

            'is_university'=>true,

            'score'=>70

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










    private function detectFileType($url)
    {


        return str_contains(

            strtolower($url),

            '.pdf'

        )

        ?

        'PDF'

        :

        'WEB';


    }










    private function isUniversity($url)
    {


        $text=strtolower($url);



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


        $score=0;



        $text=strtolower(

            $title.' '.$url

        );




        if(str_contains($text,'pdf'))

        {

            $score+=30;

        }



        if(

            str_contains($text,'univ')

            ||

            str_contains($text,'universite')

            ||

            str_contains($text,'.edu')

        )

        {

            $score+=30;

        }




        if(str_contains($text,'master'))

        {

            $score+=20;

        }




        if(

            str_contains($text,'cours')

            ||

            str_contains($text,'course')

            ||

            str_contains($text,'syllabus')

        )

        {

            $score+=20;

        }



        return $score;


    }



}