<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{

    protected $table = 'schedules';


    protected $fillable = [

        'title',

        'file_path',

        'type',

        'description',

    ];

}