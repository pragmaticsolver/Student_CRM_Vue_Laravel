<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentPaperTestRequest extends FormRequest
{
    public function rules()
    {
        return [
            'student_id' => 'required',
            'paper_test_id' => 'required',
            'date' => 'required|date',
            'score' => 'required|numeric|min:0',
            'total_score' => 'required|numeric|min:0|gte:score'
        ];
    }
}
