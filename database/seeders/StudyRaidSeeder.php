<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudyRaidSource;

class StudyRaidSeeder extends Seeder
{

    public function run()
    {

        $courses = [

            [
                'title' => 'Généralités sur les finances publiques',

                'url' => 'https://app.studyraid.com/fr/read/122635/5696968/quest-que-les-finances-publiques',

                'subject' => 'Finances',

            ],


            [
                'title' => 'Distinction finances publiques et finances privées',

                'url' => 'URL_REELLE_A_METTRE',

                'subject' => 'Finances',

            ],



            [
                'title' => 'Les principes des lois de finances',

                'url' => 'URL_REELLE_A_METTRE',

                'subject' => 'Finances',

            ],



            [
                'title' => 'La loi de finances de l’année (LFI)',

                'url' => 'URL_REELLE_A_METTRE',

                'subject' => 'Finances',

            ],



            [
                'title' => 'Les lois de finances rectificatives',

                'url' => 'URL_REELLE_A_METTRE',

                'subject' => 'Finances',

            ],



            [
                'title' => 'La loi de règlement',

                'url' => 'URL_REELLE_A_METTRE',

                'subject' => 'Finances',

            ],

        ];



        foreach($courses as $course)
        {

            StudyRaidSource::updateOrCreate(

                [
                    'title'=>$course['title']
                ],

                [

                    'url'=>$course['url'],

                    'subject'=>$course['subject'],

                    'active'=>true,

                ]

            );

        }

    }

}