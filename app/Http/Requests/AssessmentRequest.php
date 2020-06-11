<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentRequest extends FormRequest
{
    public function rules()
    {
        if($this->route('id')) // edit
        {
            return [
                'name' => 'required|max:191'
            ];
        }
        else // create
        {
            return [
                'name' => 'required|max:191',
                'assessment_type' => 'required',
                'course_id' => 'required_if:assessment_type,automatic',
                'unit_id' => 'required_if:assessment_type,automatic',
                'lesson_id' => 'required_if:assessment_type,automatic',
                'send_to' => 'required_if:assessment_type,automatic'
            ];
        }
    }
}
