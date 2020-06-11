<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Students;

class StudentRequest extends FormRequest
{
    public function rules()
    {
        $validate_params = [
            'lastname' => 'required',
            'firstname' => 'required',
            'email' => 'required|email',
            'role' => 'required'
        ];

        // if($this->route('student')) {
        //     $student = Students::find($this->route('student'));

        //     $validate_params['email'] = 'required|unique:users,email,'.$student->user_id;
        // } else {
        //     $validate_params['email'] = 'required|unique:users,email';
        // }

        return $validate_params;
    }
}
