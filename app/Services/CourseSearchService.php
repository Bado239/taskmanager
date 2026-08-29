<?php

namespace App\Services;

class CourseSearchService
{

    public function search($subject, $level = 'Master 1')
    {

        /*
         Première version :
         recherche dans les sources ouvertes
         */

        return [

            [
                'title' => 'Corporate Finance',
                'source' => 'MIT OpenCourseWare',
                'url' => 'https://ocw.mit.edu',
                'type' => 'Cours'
            ],

            [
                'title' => 'Financial Management',
                'source' => 'Yale Open Courses',
                'url' => 'https://oyc.yale.edu',
                'type' => 'Cours'
            ],

            [
                'title' => 'Finance d’entreprise',
                'source' => 'UCAD Sénégal',
                'url' => 'https://www.ucad.sn',
                'type' => 'Cours'
            ],

        ];

    }

}