<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseResource;
use App\Models\ExamPrep;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectStep;
use App\Models\LearningDocument;
use App\Models\GeneratedCourse;
use App\Models\StudyRaidSource;

/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string|null $project_name
 * @property int|null $study_raid_source_id
 */


class Task extends Model
{


    /**
     * Les attributs assignables en masse.
     */

    protected $fillable = [

        'title',

        'category_id',

        'project_id',

        'project_name',

        'project_step_id',

        'priority',

        'date_prevue',

        'execution_date',

        'heure_debut',

        'heure_fin',

        'document_link',

        'progress',

        'status',

        'type',

        'document_status',

        'is_archived',

        'study_raid_source_id',

    ];




    /**
     * Conversion des attributs.
     */

    protected $casts = [

        'is_archived' => 'integer',

        'date_prevue' => 'date',

        'execution_date' => 'date',

    ];





    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */



    /**
     * Suivi examen Master
     */

    public function examPrep()
    {

        return $this->hasOne(ExamPrep::class);

    }





    /**
     * Catégorie
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





    /**
     * Étape du projet
     */

    public function step()
    {

        return $this->belongsTo(
            ProjectStep::class,
            'project_step_id'
        );

    }





    /**
     * Ressources pédagogiques
     */

    public function courseResources()
    {

        return $this->hasMany(
            CourseResource::class
        );

    }





    /**
     * Documents personnels
     */

    public function learningDocuments()
    {

        return $this->hasMany(
            LearningDocument::class
        );

    }





    /**
     * Cours généré par IA / StudyRaid
     */

    public function generatedCourse()
    {

        return $this->hasOne(
            GeneratedCourse::class
        );

    }





    /**
     * Source StudyRaid associée
     */

    public function studyRaidSource()
    {

        return $this->hasOne(
            StudyRaidSource::class
        );

    }



}