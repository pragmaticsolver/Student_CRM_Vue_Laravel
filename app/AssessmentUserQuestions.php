<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentUserQuestions extends Model
{
    protected $table = 'assessment_user_questions';
    
    protected $fillable = [
        'assessment_user_id', 'assessment_question_id', 'value'
    ];

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
