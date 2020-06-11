<?php

namespace App;

use App\Helpers\MailHelper;
use Illuminate\Database\Eloquent\Model;

class AssessmentUsers extends Model
{
    protected $table = 'assessment_users';
    
    public const INCOMPLETE_STATUS = 0;
    public const COMPLETE_STATUS = 1;

    protected $fillable = [
        'user_id', 'assessment_id', 'schedule_id', 'complete', 'for_student'
    ];

    public $timestamps = false;

    public function assessment()
    {
        return $this->belongsTo('App\Assessments', 'assessment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function assessment_user_questions()
    {
        return $this->hasMany('App\AssessmentUserQuestions', 'assessment_user_id', 'id');
    }

    public function is_complete()
    {
        return $this->complete ? true : false;
    }

    public function assessment_for_student()
    {
        return $this->hasOne('App\Students', 'id', 'for_student');
    }

    public function assessmentUserTimeslots()
    {
        return $this->hasMany('App\AssessmentUserTimeslot', 'assessment_user_id', 'id');
    }
}
