<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassCategoryRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->route('class_category');
        return [
            'name' => $id ? 'required|max:191|unique:class_categories,name,'.$id : 'required|max:191|unique:class_categories,name',
            'visible_user_roles' => 'required'
        ];
    }
}
