<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingGoal extends Model
{

    protected $fillable = [

        'personal_resource_id',

        'date',

        'start_page',

        'target_page',

        'daily_pages',

        'delay_pages',

        'calculation_start_page',

        'status'

    ];


    public function book()
    {

        return $this->belongsTo(
            PersonalResource::class,
            'personal_resource_id'
        );

    }

}