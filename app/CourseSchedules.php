<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseSchedules extends Model
{
    protected $table = 'course_schedules';
    
    protected $fillable = [
        'course_id', 'schedule_id'
    ];

    public $timestamps = false;

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses', 'course_id', 'id');
    }
}
