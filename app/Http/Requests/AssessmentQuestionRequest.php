<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentQuestionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'assessment_id' => 'required',
            'assessment_question_type' => 'required',
            'options' => 'required_if:assessment_question_type,option'
        ];
    }
}
