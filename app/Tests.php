<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tests extends Model
{
    protected $table = 'tests';
    
    protected $fillable = [
        'name', 'course_id', 'unit_id', 'lesson_id'
    ];

    public $timestamps = false;

    public function course()
    {
        return $this->belongsTo('App\Courses', 'course_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Units', 'unit_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Lessons', 'lesson_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany('App\Questions', 'test_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany('App\Answers', 'test_id', 'id');
    }

    public function student_tests()
    {
        return $this->hasMany('App\StudentTests', 'test_id', 'id');
    }

    public function get_total_score()
    {
        return $this->questions->sum('score');
    }
}
