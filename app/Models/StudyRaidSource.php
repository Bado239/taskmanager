<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $task_id
 * @property string $title
 * @property string $url
 * @property string|null $subject
 * @property bool $active
 */

class StudyRaidSource extends Model
{


    protected $fillable = [

        'task_id',
        'title',
        'url',
        'subject',
        'active',

    ];



    protected $casts = [

        'active'=>'boolean',

    ];




    public function task()
    {
        return $this->belongsTo(Task::class);
    }





    /*
    |--------------------------------------------------------------------------
    | Recherche automatique d'une source par titre
    |--------------------------------------------------------------------------
    */


    public static function findByTitle($title)
    {

        return self::whereRaw(
                '"active" = true'
            )
            ->whereRaw(
                'LOWER(title) LIKE ?',
                [
                    '%'.mb_strtolower($title, 'UTF-8').'%'
                ]
            )
            ->first();

    }

}