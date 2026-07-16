<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    // On met 'name' dans le fillable puisque notre migration utilise $table->string('name')
    protected $fillable = ['name', 'description'];

    /**
     * Un projet possède plusieurs étapes.
     */
    public function steps()
    {
        return $this->hasMany(ProjectStep::class);
    }

    /**
     * Un projet possède plusieurs tâches.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}