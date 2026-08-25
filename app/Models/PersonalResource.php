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

    ];


    protected $casts = [

        'id' => 'integer',

        'is_active' => 'boolean',

    ];


    /**
     * Correction PostgreSQL BOOLEAN
     */
    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = $value ? 'true' : 'false';
    }

}