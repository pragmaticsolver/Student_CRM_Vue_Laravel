<?php

namespace App;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ScheduleComment extends Model
{
    protected $table = 'schedule_comments';
    
    protected $fillable = [
        'schedule_id', 'class_date', 'user_id', 'comment'
    ];

    public $timestamps = false;

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function getLocalCommentUpdatedAt()
    {
        return Carbon::createFromFormat("Y-m-d H:i:s", $this->updated_at, 'UTC')->setTimezone(CommonHelper::getSchoolTimezone());
    }
}
