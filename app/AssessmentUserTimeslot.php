<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentUserTimeslot extends Model
{
    protected $table = 'assessment_user_timeslots';
    
    public $timestamps = false;

    public function assessment_user()
    {
        return $this->belongsTo('App\AssessmentUsers', 'assessment_user_id', 'id');
    }

    public function assessment_question()
    {
        return $this->belongsTo('App\AssessmentQuestions', 'assessment_question_id', 'id');
    }
}
