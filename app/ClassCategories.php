<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassCategories extends Model
{
    protected $table = 'class_categories';
    
    protected $fillable = ['name', 'visible_user_roles'];

    public $timestamps = false;

    public function classes()
    {
        return $this->hasMany('App\Classes', 'category_id', 'id');
    }

    public function get_user_roles_label()
    {
        $label = '';

        $user_roles = json_decode($this->visible_user_roles);

        if($user_roles) {
            $label = implode(', ', $user_roles);
        }

        return $label;
    }

    public function get_classes()
    {
        return $this->classes()->where('class_type', Classes::CLASS_TYPE)->get();
    }

    public function get_events()
    {
        return $this->classes()->where('class_type', Classes::EVENT_TYPE)->get();
    }
}
