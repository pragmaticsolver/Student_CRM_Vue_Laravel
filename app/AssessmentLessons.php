<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentLessons extends Model
{
    protected $table = 'assessment_lessons';
    
    public const SEND_TO_STUDENT = 'student';
    public const SEND_TO_TEACHER = 'teacher';

    protected $fillable = [
        'course_id', 'unit_id', 'lesson_id', 'assessment_id', 'send_to'
    ];

    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo('App\Courses', 'course_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Units', 'unit_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Lessons', 'lesson_id', 'id');
    }

    public function assessment()
    {
        return $this->belongsTo('App\Assessments', 'assessment_id', 'id');
    }
}
