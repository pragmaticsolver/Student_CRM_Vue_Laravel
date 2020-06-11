<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelectionCalenderTimeSlot extends Model
{
    protected $table = 'selection_calender_time_slots';
    
    public $timestamps = false;

    public function toFullcalendarFormat()
    {
        return [
            'id' => $this->id,
            'daysOfWeek' => [ $this->day_of_week ],
            'startTime' => $this->from,
            'endTime' => $this->to
        ];
    }
}
