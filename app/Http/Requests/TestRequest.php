<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:191',
            'test_type' => 'required',
            'course_id' => 'required',
            'unit_id' => 'required',
            'lesson_id' => 'required',
            'total_score' => 'required_if:test_type,paper'
        ];
    }
}
