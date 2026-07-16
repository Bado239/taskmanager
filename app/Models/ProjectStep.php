<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStep extends Model
{
    // Champs que l'on peut remplir via ProjectStep::create()
    protected $fillable = ['project_id', 'name'];

    /**
     * Une étape appartient à un projet.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}