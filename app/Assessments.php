<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessments extends Model
{
    protected $table = 'assessments';
    
    public const AUTOMATIC_TYPE = 'automatic';
    public const MANUAL_TYPE = 'manual';

    protected $fillable = [
        'name', 'type', 'description'
    ];

    public $timestamps = false;

    public function assessment_questions()
    {
        return $this->hasMany('App\AssessmentQuestions', 'assessment_id', 'id')->orderBy('position');
    }

    public function assessment_users()
    {
        return $this->hasMany('App\AssessmentUsers', 'assessment_id', 'id');
    }

    public function assessment_lesson()
    {
        return $this->hasOne('App\AssessmentLessons', 'assessment_id', 'id');
    }

    public function lessons()
    {
        return $this->belongsToMany('App\Lessons', 'assessment_lessons', 'assessment_id', 'lesson_id');
    }

    public static function get_all_manual()
    {
        return self::where('type', 'manual')->get();
    }
}
