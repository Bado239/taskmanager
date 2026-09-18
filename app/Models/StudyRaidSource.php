<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class StudyRaidSource extends Model
{

    protected $fillable = [

        'task_id',
        'url'

    ];



    public function task()
    {

        return $this->belongsTo(Task::class);

    }

}