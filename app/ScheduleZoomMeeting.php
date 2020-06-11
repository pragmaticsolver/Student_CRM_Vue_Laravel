<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleZoomMeeting extends Model
{
    protected $table = 'schedule_zoom_meetings';
    
    public $timestamps = false;

    public function zoomMeeting()
    {
        return $this->hasOne('App\ZoomMeeting', 'id', 'zoom_meeting_id');
    }

    public function schedule()
    {
        return $this->belongsTo('App\Schedules', 'schedule_id', 'id');
    }
}
