<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        $user_id = $this->route('user');

        if($user_id) {
            $params = [
                'name' => 'required|max:120',
                'username' => 'required|alpha_dash|unique:users,username,'.$user_id,
            ];

            $user = User::find($user_id);
            if(!($user && $user->willUseParentEmail()))
            {
                $params['email'] = 'required|email';
            }

            if(request('change_password') == 'on')
            {
                $params['password'] = 'required_if:change_password,on|min:6|confirmed';
            }

            return $params;
        } else {
            return [
                'name' => 'required|max:120',
                'email' => 'required|email',
                'username' => 'required|alpha_dash|unique:users,username',
                'password' => 'required|min:6|confirmed',
                'role' => 'required'
            ];
        }
    }
}
