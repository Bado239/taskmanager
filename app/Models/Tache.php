<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $fillable = [
        'projet_id',
        'titre',
        'description',
        'date_debut',
        'date_limite',
        'priorite',
        'statut',
        'avancement'
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
}