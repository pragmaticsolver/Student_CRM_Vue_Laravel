<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Teachers;

class TeacherRequest extends FormRequest
{
    public function rules()
    {
        if($this->route('teacher')) {
            $teacher = Teachers::find($this->route('teacher'));

            return [
                'fullname'=>'required',
                'furigana'=> 'required',
                'username'=> 'required|unique:users,username,'.$teacher->user->id.'|alpha_dash',
                'nickname'=> 'required|alpha_dash',
                'email' => 'required|unique:users,email,'.$teacher->user->id,
                'zoom_email' => 'nullable|email',
                'birthday'=> 'required',
                'birthplace'=> 'required',
                'profile' => 'required',
            ];
        } else {
            return [
                'fullname'=>'required',
                'furigana'=> 'required',
                'username'=> 'required|unique:users,username|alpha_dash',
                'nickname'=> 'required|alpha_dash',
                'email' => 'required|unique:users,email',
                'zoom_email' => 'nullable|email',
                'birthday'=> 'required',
                'birthplace'=> 'required',
                'profile' => 'required',
                'password' => 'required|min:6|confirmed'
            ];
        }
    }
}
