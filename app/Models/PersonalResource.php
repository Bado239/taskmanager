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
        'is_active',
    ];
}