<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonFile extends Model
{
    protected $table = 'lesson_files';
    
    public $timestamps = false;

    public function lesson()
    {
        return $this->belongsTo('App\Lessons', 'lesson_id', 'id');
    }

}
