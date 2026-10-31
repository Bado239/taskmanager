<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property bool $is_active
 */
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'is_active',
    ];

    // Force Laravel à traiter is_active comme un vrai booléen pour PostgreSQL
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}