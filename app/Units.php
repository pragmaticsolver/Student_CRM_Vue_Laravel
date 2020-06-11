<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    protected $table = 'units';
    
    protected $fillable = [
        'name', 'course_id', 'objectives', 'position'
    ];

    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo('App\Courses', 'course_id', 'id');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lessons', 'unit_id', 'id')->orderBy('position','ASC');
    }

    public function tests()
    {
        return $this->hasMany('App\Tests', 'unit_id', 'id');
    }

    public static function get_validate_params()
    {
        return [
            'name' => 'required',
            'course_id' => 'required',
            'objectives' => 'required'
        ];
    }
}
