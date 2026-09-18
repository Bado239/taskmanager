<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $task_id
 * @property string $title
 * @property string $type
 * @property string|null $url
 */

class LearningDocument extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'type',
        'file_path',
        'url',
    ];

    /**
     * @property string|null $file_path
     * @property string|null $url
     */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}