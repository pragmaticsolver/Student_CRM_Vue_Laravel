<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => $this->route('role') ?
                'required|unique:roles,name,'.$this->route('role').'|max:20' :
                'required|unique:roles,name|max:20',
            //'permissions' => 'required',
            'login_redirect_path' => 'required|max:191'
        ];
    }
}
