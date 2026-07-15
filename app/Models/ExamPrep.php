<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamPrep extends Model
{
    protected $fillable = [
        'task_id', 
        'course_reviewed', 
        'summary_done', 
        'exercises_done', 
        'past_papers_done'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}