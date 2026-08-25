<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'status',
        'author_or_source',
        'pdf_path',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}