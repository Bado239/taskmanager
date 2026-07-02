<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'date_debut',
        'date_fin',
        'responsable_id'
    ];

    public function taches()
    {
        return $this->hasMany(Tache::class);
    }
}
