<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    protected $table = 'zoom_meetings';
    
    public $timestamps = false;

    public function getDisplayMeetingIdAttribute()
    {
        $id = (string) $this->pmi ? $this->pmi : $this->id;
        
        if(strlen($id) <= 10)
        {
            $display_meeting_id = substr($id,0,3).' '.substr($id,3,3). ' '.substr($id,6);
        }
        else
        {
            $display_meeting_id = substr($id,0,3).' '.substr($id,3,4). ' '.substr($id,7);
        }
        
        return $display_meeting_id;
    }

    public static function create($meeting)
    {
        $start_time = $meeting['start_time'];
        $start_time = str_replace("T"," ",$start_time);
        $start_time = str_replace("Z","",$start_time);

        $zoomMeeting = new ZoomMeeting();
        $zoomMeeting->id = $meeting['id'];
        $zoomMeeting->pmi = isset($meeting['pmi']) ? $meeting['pmi'] : NULL;
        $zoomMeeting->password = isset($meeting['password']) ? $meeting['password'] : NULL;
        $zoomMeeting->start_time = $start_time;
        $zoomMeeting->start_url = $meeting['start_url'];
        $zoomMeeting->join_url = $meeting['join_url'];
        $zoomMeeting->save();

        return $meeting['id'];
    }

    public function syncFromZoom($meeting)
    {
        $start_time = $meeting['start_time'];
        $start_time = str_replace("T"," ",$start_time);
        $start_time = str_replace("Z","",$start_time);

        $zoomMeeting = $this;
        $zoomMeeting->pmi = isset($meeting['pmi']) ? $meeting['pmi'] : NULL;
        $zoomMeeting->password = isset($meeting['password']) ? $meeting['password'] : NULL;
        $zoomMeeting->start_time = $start_time;
        $zoomMeeting->start_url = $meeting['start_url'];
        $zoomMeeting->join_url = $meeting['join_url'];
        $zoomMeeting->save();
    }

    public function updatedFromZoom($meeting)
    {
        $zoomMeeting = $this;

        if (isset($meeting['pmi'])) {
            $zoomMeeting->pmi = $meeting['pmi'];
        }

        if (isset($meeting['password'])) {
            $zoomMeeting->password = $meeting['password'];
        }

        if (isset($meeting['start_time'])) {

            $start_time = $meeting['start_time'];
            $start_time = str_replace("T"," ",$start_time);
            $start_time = str_replace("Z","",$start_time);

            $zoomMeeting->start_time = $start_time;
        }
        
        if (isset($meeting['start_url'])) {
            $zoomMeeting->start_url = $meeting['start_url'];
        }
        
        if (isset($meeting['join_url'])) {
            $zoomMeeting->join_url = $meeting['join_url'];
        }

        $zoomMeeting->save();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['display_meeting_id'] = $this->display_meeting_id;
        return $array;
    }
}
