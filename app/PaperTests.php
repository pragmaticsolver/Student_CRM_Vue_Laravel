<?php

namespace App;

use App\Helpers\MailHelper;
use Illuminate\Database\Eloquent\Model;

class PaperTests extends Model
{
    protected $table = 'paper_tests';
    
    protected $fillable = [
        'name', 'course_id', 'total_score', 'unit_id', 'lesson_id'
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

    public function student_paper_tests()
    {
        return $this->hasMany('App\StudentPaperTests', 'paper_test_id', 'id');
    }
}
