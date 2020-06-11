<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OnlineTestRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:191',
            'course_id' => 'required',
            'unit_id' => 'required',
            'lesson_id' => 'required'
        ];
    }
}
