<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyRaidSource extends Model
{

    protected $fillable = [
        'task_id',
        'title',
        'url',
        'subject',
        'active',
        'level',
        'provider',
    ];


    protected $casts = [
        'active' => 'boolean',
    ];


    public function task()
    {
        return $this->belongsTo(Task::class);
    }


}