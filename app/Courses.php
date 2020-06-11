<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Courses extends Model
{
    protected $table = 'courses';
    
    protected $fillable = [
    	'title',
        'description',
        'objectives',
    	'thumbnail'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

    public function get_image() 
    {
        return Storage::disk('public')->url('course/'.$this->thumbnail);
    }

    public function the_image() {
        return '<img src="'.$this->get_image().'" width=100 height=100>';
    }

    public function units()
    {
        return $this->hasMany('App\Units', 'course_id', 'id')->orderBy('position','ASC');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lessons', 'course_id', 'id');
    }

    public function tests()
    {
        return $this->hasMany('App\Tests', 'course_id', 'id');
    }

    public function course_schedules()
    {
        return $this->hasMany('App\CourseSchedules', 'course_id', 'id');
    }

    public function paper_tests()
    {
        return $this->hasMany('App\PaperTests', 'course_id', 'id');
    }

    public static function get_store_validate_params()
    {
        return [
            'title'=>'required|unique:courses'
        ];
    }

    public static function get_update_validate_params($id)
    {
        return [
            'title'=>'required|unique:courses,title,'.$id
        ];
    }
}
