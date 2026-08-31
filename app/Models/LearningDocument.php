<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningDocument extends Model
{

    protected $fillable = [

        'task_id',
        'title',
        'type',
        'file_path',
        'url'

    ];



    public function task()
    {

        return $this->belongsTo(Task::class);

    }

}