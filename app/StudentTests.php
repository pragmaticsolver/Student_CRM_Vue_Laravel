<?php

namespace App;

use App\Helpers\MailHelper;
use Illuminate\Database\Eloquent\Model;

class StudentTests extends Model
{
    protected $table = 'student_tests';
    
    public const INCOMPLETE_STATUS = 0;

    protected $fillable = [
        'student_id', 'test_id', 'schedule_id', 'total_score', 'score', 'status', 'comment',
    ];

    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\Students', 'student_id', 'id');
    }

    public function test()
    {
        return $this->belongsTo('App\Tests', 'test_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function valid_undone()
    {
        if($this->status == 1) throw new \Exception(__('messages.alreadycomplete'));
    }

    public function is_complete()
    {
        return $this->status ? true : false;
    }
}
