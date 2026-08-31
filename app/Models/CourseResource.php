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
        'searched_at',
        'rating',
        'notes'

    ];


    protected $casts = [

        'is_university' => 'boolean',

        'saved' => 'boolean',

        'score' => 'integer',

        'relevance' => 'integer',

        'rating' => 'integer',

        'order' => 'integer',

        'searched_at' => 'datetime',

    ];


    /**
     * Correction PostgreSQL BOOLEAN
     */
    public function setIsUniversityAttribute($value)
    {
        $this->attributes['is_university'] =
            $value ? 'true' : 'false';
    }


    /**
     * Correction PostgreSQL BOOLEAN
     */
    public function setSavedAttribute($value)
    {
        $this->attributes['saved'] =
            $value ? 'true' : 'false';
    }


    /**
     * Relation avec la tâche
     */
    public function task()
    {
        return $this->belongsTo(
            Task::class
        );
    }


    /**
     * Filtrer les ressources d'une tâche
     */
    public function scopeForTask($query, $taskId)
    {
        return $query->where(
            'task_id',
            $taskId
        );
    }

}