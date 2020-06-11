<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaperTestRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:191',
            'total_score' => 'required|numeric|min:0',
            'course_id' => 'required',
            'unit_id' => 'required',
            'lesson_id' => 'required'
        ];
    }
}
