<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
    'title', 
    'category_id', 
    'project_id', 
    'priority', 
    'date_prevue', 
    'heure_debut', 
    'heure_fin', 
    'document_link', 
    'progress', 
    'status',
    'type',              // Ajouté
    'document_status',   // Ajouté
];

// Relation avec le suivi d'examen (Master)
public function examPrep()
{
    return $this->hasOne(ExamPrep::class);
}

    /**
     * Catégorie de la tâche
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Projet associé
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}