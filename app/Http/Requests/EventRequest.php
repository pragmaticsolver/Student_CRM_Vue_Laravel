<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->route('event');

        return [
            'title' => $id ? 'required|unique:classes,title,'.$id : 'required|unique:classes,title',
            'date' => 'required',
            'cost' => 'required|min:0',
            'size' => 'required|min:1',
            'category_id' => 'required',
            'level' => 'required'
        ];
    }
}
