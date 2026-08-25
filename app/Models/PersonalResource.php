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

    ];




    protected $casts = [

        'id' => 'integer',

        'is_active' => 'boolean',

        'current_page' => 'integer',

        'progress' => 'integer',

    ];





    /**
     * Correction PostgreSQL BOOLEAN
     *
     * PostgreSQL attend true/false
     * et non 1/0
     */
    public function setIsActiveAttribute($value)
    {

        $this->attributes['is_active'] =
            $value ? 'true' : 'false';

    }



}