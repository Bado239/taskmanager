<?php

namespace App\Services;

use App\Models\ReadingGoal;
use Carbon\Carbon;


class ReadingProgressService
{


    public function calculate($book, $goal)
    {


        $today = Carbon::today();


        $startDate = Carbon::parse(
            $goal->date
        );


        // Nombre de jours depuis le départ

        $days = $startDate->diffInDays($today);



        // Pages prévues

        $expectedPages =
            $days * $goal->daily_pages;



        // Page théorique à atteindre

        $expectedPage =
            $goal->calculation_start_page
            +
            $expectedPages;



        // Différence réelle

        $difference =
            $book->current_page
            -
            $expectedPage;



        if($difference > 0)
        {

            return [

                'status'=>'ahead',

                'message'=>
                "🚀 En avance de ".$difference." pages"

            ];

        }


        if($difference == 0)
        {

            return [

                'status'=>'normal',

                'message'=>
                "🟢 Dans le rythme"

            ];

        }


        return [

            'status'=>'late',

            'message'=>
            "🔴 Retard de ".abs($difference)." pages"

        ];

    }

}