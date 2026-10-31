<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Exam extends Model
{
    
    protected $fillable = [
        'project_id',
        'exam_date',
        'start_time',
        'duration_minutes',
        'target_score',
        'notes',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'duration_minutes' => 'integer',
        'target_score' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
