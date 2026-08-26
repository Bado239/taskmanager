<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalResource extends Model
{

    protected $table = 'personal_resources';



    protected $fillable = [

        'type',

        'title',

        'description',

        'status',

        'author_or_source',

        'is_active',

        'content',

        'notes',

        'pdf_path',


        // Progression lecture
        'current_page',

        'progress',


        // Gestion bibliothèque
        'reading_status',

    ];




    protected $casts = [

        'id' => 'integer',

        'is_active' => 'boolean',

        'current_page' => 'integer',

        'progress' => 'integer',

    ];





    /**
     * Valeurs par défaut
     */
    protected $attributes = [

        'reading_status' => 'library',

        'progress' => 0,

        'current_page' => 1,

    ];





    /**
     * Correction PostgreSQL BOOLEAN
     */
    public function setIsActiveAttribute($value)
    {

        $this->attributes['is_active'] =
            $value ? 'true' : 'false';

    }


}