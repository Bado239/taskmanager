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
        'project_name',      // Nom du projet sauvegardé (persiste même si le projet est supprimé)
        'project_step_id',
        'priority',
        'date_prevue',
        'execution_date',
        'heure_debut',
        'heure_fin',
        'document_link',
        'progress',
        'status',
        'type',
        'document_status',
        'is_archived',
    ];

    /**
     * Les attributs qui doivent être convertis.
     */
    protected $casts = [
        'is_archived' => 'integer',
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