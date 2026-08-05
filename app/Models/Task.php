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
        'project_step_id',   // Étape liée au projet
        'priority', 
        'date_prevue',       // Correspond à la date en base de données
        'heure_debut',       // Correspond à l'heure de début en base de données
        'heure_fin',         // Correspond à l'heure de fin en base de données
        'document_link', 
        'progress', 
        'status',
        'type',              // Mode de l'application (master ou office)
        'document_status',   // Statut du livrable
    ];

    /**
     * Relation avec le suivi d'examen (Master)
     */
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

    /**
     * Étape de projet associée
     */
    public function step()
    {
        return $this->belongsTo(ProjectStep::class, 'project_step_id');
    }
}