<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedCourse extends Model
{

protected $fillable = [

'task_id',
'title',
'content'

];


public function task()
{
    return $this->belongsTo(Task::class);
}

}