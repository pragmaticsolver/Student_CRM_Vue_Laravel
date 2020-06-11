<?php

namespace App\Http\Requests;

use App\Settings;
use Illuminate\Foundation\Http\FormRequest;

class ClassRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->route('class');
        $rules = array();
        if($id)
        {
            $rules['title'] = 'required|unique:classes,title,'.$id;
        }
        else
        {
            $rules['title'] = 'required|unique:classes';
            if(Settings::get_value('use_points') == 'true')
            {
                $rules['payment_plan_id'] = 'required';
            }
        }
        $rules['category_id'] = 'required';
        $rules['level'] = 'required';

        return $rules;
    }
}
