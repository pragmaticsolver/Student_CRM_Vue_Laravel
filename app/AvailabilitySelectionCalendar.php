<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AvailabilitySelectionCalendar extends Model
{
    protected $table = 'availability_selection_calendars';
    
    public $timestamps = false;

    public function selectionCalendarTimeSlots()
    {
        return $this->hasMany('App\SelectionCalenderTimeSlot', 'calendar_id', 'id');
    }
}
