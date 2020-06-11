<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'assessment_id' => 'required',
            'send_to' => 'required',
            'students' => 'required'
        ];
    }
}
