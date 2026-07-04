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
        'priority', // 🔴 AJOUT UNIQUE : Permet d'enregistrer la priorité choisie (high, medium, low)
        'date_prevue',
        'heure_debut', // Autorise l'enregistrement de l'heure de début
        'heure_fin',   // Autorise l'enregistrement de l'heure de fin
    ];

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