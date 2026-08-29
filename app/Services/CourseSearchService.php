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


            if (count($courses) >= 5) {
                return;
            }


            if (
                !$node->filter('.result__title')->count()
                ||
                !$node->filter('.result__a')->count()
            ) {
                return;
            }



            $title = $node
                ->filter('.result__title')
                ->text();



            $url = $this->cleanUrl(
                $node->filter('.result__a')->attr('href')
            );

            $courses[] = [

                'title' => trim($title),

                'source' => str_replace(
                    'www.',
                    '',
                    parse_url($url, PHP_URL_HOST)
                ),

                'url' => $url,

                'type' => 'Cours Web'

            ];

        });



        return $courses;

    }

    private function cleanUrl($url)
    {
        if(str_contains($url, 'uddg='))
        {
            parse_str(parse_url($url, PHP_URL_QUERY), $query);

            if(isset($query['uddg']))
            {
                return urldecode($query['uddg']);
            }
        }


        return $url;
    }

}