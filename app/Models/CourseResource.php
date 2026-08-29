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
    'score',
    'relevance',
    'saved',
    'searched_at'

    ];


    protected $casts = [

        'is_university' => 'boolean',

        'score' => 'integer',

        'order' => 'integer',

    ];



    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}