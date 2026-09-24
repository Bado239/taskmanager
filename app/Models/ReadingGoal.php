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
        'pages_per_day',
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