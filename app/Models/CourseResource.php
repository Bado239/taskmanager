<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseResource extends Model
{
    protected $fillable = [

        'task_id',

        'title',

        'source',

        'url',

        'type',

        'order',

        'file_type',

        'is_university',

        'score'

    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}