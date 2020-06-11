<?php

namespace App;

use App\Helpers\ActivityEnum;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    
    public $timestamps = false;

    public function activity()
    {
        return $this->belongsTo('App\Activity','activity_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function get_datetime($timezone)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at, 'UTC')->setTimezone($timezone);
    }

    public function get_creted_by_user_name()
    {
        $activity_by = @$this->user->name;
        return $activity_by;
    }

    public function get_disaplay_text()
    {
        $out = "";
        $out .= $this->activity->get_display_name();
        $details = $this->get_activity_details();
        if($details)
        {
            $out .= " ( " . $details . " )";
        }
        return $out;
    }

    public function get_activity_details()
    {
        $desc = "";
        $detail_params = $this->detail_params ? json_decode($this->detail_params) : NULL;
        if($detail_params)
        {
            $des_array = [];
            foreach($detail_params as $key => $value)
            {
                $des_array[] = "$key: $value";
            }
            $desc = implode(", ",$des_array);
        }
        return $desc;
    }
}
