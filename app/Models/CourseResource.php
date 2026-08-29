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
        'order'
    ];


    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}