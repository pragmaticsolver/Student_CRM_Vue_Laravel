<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestions extends Model
{
    protected $table = 'assessment_questions';
    
    protected $fillable = [
        'name', 'type', 'assessment_id', 'option_values', 'position', 'availability_selection_calendar_id', 'is_required'
    ];

    public $timestamps = false;

    public static function getQuestionTypes()
    {
        return[
            'rating' => __('messages.rating'),
            'option' => __('messages.option'),
            'comment' => __('messages.comment'),
            'availability-selection-calender' => __('messages.availability-selection-calendar'),
            'textfield' => __('messages.text-field')
        ];
    }

    public function getDislayType()
    {
        return self::getQuestionTypes()[$this->type];
    }

    public function assessment()
    {
        return $this->belongsTo('App\Assessments', 'assessment_id', 'id');
    }

    public function assessment_user_questions()
    {
        return $this->hasMany('App\AssessmentUserQuestions', 'assessment_question_id', 'id');
    }

    public function availabilitySelectionCalendar()
    {
        return $this->hasOne('App\AvailabilitySelectionCalendar', 'id', 'availability_selection_calendar_id');
    }

    public function getUserAnswer($assessment_user_id)
    {
        if($this->type == 'availability-selection-calender')
        {
            $selected_timeslot_ids = AssessmentUserTimeslot::where('assessment_user_id', $assessment_user_id)
                                                        ->where('assessment_question_id', $this->id)
                                                        ->pluck('timeslot_id')->toArray();
            return implode(",",$selected_timeslot_ids);                                               
        }
        else
        {
            $userAnswerRaw = AssessmentUserQuestions::where('assessment_question_id', $this->id)->where('assessment_user_id', $assessment_user_id)->first();
            if($userAnswerRaw)
            {
                return $userAnswerRaw->value;
            }
            else
            {
                return NULL;
            }
        }
    }
}
