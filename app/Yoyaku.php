<?php

namespace App;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Yoyaku extends Model
{
    protected $table = 'yoyakus';
    
  	protected $fillable = [
    	'customer_id',
    	'schedule_id',
    	'date',
    	'taiken',
    	'status',
    	'start_date',
    	'end_date',
        'waitlist'
  	];

  	/**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

  	public static function countDate($yoyakus) {
  	    $date = [];
  	    if($yoyakus) {
            foreach($yoyakus as $yoyaku) {
                if(empty($date[$yoyaku->date])) $date[$yoyaku->date] = 0;
                $date[$yoyaku->date]++;
            }
        }
        return $date;
    }

    public function schedule() {
  	    return $this->hasOne('App\Schedules', 'id', 'schedule_id');
    }

    public function student() {
  	    return $this->hasOne('App\Students', 'id', 'customer_id');
    }

    public function attendance()
    {
        return $this->hasOne('App\Attendances', 'yoyaku_id', 'id');
    }

    public function classUsage()
    {
        return $this->hasOne('App\ClassUsage','yoyaku_id','id');
    }

    public function getAttendanceStatusAttribute()
    {
        $status = "";
        if($this->waitlist == 1)
        {
            $status = 'Waitlisted';
        }
        else if($this->status == 0)
        {
            $status = 'Reserved';
        }
        else if($this->status  == 1)
        {
            $status = 'Signed In';
        }
        else if($this->status == 2)
        {
            @$attendance_status = $this->attendance->cancellationPolicy->cancelType->alias;
            $status = ucwords(str_replace("-"," ",$attendance_status));
        }
        return $status;
    }

    /**
     * Get all the reservations will be happened tomorrow
     *
     * @return array
     */
    public static function get_tomorrow_reservations() {
  	    $future_reservations = self::get_future_reservations();
        $tomorrow_reservations = self::_get_tomorrow_reservations($future_reservations);
        return $tomorrow_reservations;
    }

    /**
     * Get all the reservations will be happened in the future
     *
     * @return array
     */
    public static function get_future_reservations() {
        return self::where('status', 0)->where('date', '>', Carbon::today(CommonHelper::getSchoolTimezone())->toDateString())->get();
  	}

    /**
     * Filter future reservations to get all the reservations will be happened tomorrow
     *
     * @param $future_reservations
     * @return array
     */
  	private static function _get_tomorrow_reservations($future_reservations) {
  	    $tomorrow_reservations = [];

  	    if(!$future_reservations->isEmpty()) {
            foreach($future_reservations as $reservation) {
                if(Carbon::today(CommonHelper::getSchoolTimezone())->diffInDays(new Carbon($reservation->date, CommonHelper::getSchoolTimezone())) == 1) {
                    array_push($tomorrow_reservations, $reservation);
                }
            }
        }

        return $tomorrow_reservations;
    }

    public function get_status()
    {
        if(!$this->waitlist) {
            switch($this->status) {
                case 1:
                    return __('messages.attendedstudent');
                case 2:
                    return __('messages.canceledstudent');
                default:
                    return __('messages.registeredstudent');
            }
        } else {
            return __('messages.waitliststudent');
        }
    }
}
