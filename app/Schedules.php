<?php

namespace App;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage; 

class Schedules extends Model
{
    protected $table = 'schedules';
    
    public const CLASS_REPEATED_TYPE = 0;
    public const EVENT_ALLDAY_TYPE = 2;
    public const EVENT_TIME_TYPE = 3;
    public const EVENT_TYPES = [2, 3];
    public const CLASS_TYPES = [0, 1];

  	protected $fillable = [
    	'class_id',
    	'teacher_id',
    	'start_time',
    	'end_time',
    	'date',
    	'day_of_week',
    	'type',
        'start_date',
        'end_date',
        'description'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var    bool
     */
    public $timestamps = false;

  	public function yoyaku() {
  	    return $this->hasMany('App\Yoyaku', 'schedule_id', 'id');
    }

    public function class() {
  	    return $this->hasOne('App\Classes', 'id', 'class_id');
    }

    public function event() {
        return $this->hasOne('App\Events', 'id', 'class_id');
    }

    public function teacher() {
  	    return $this->hasOne('App\Teachers', 'id', 'teacher_id');
    }

    public function schedule_lessons()
    {
        return $this->hasMany('App\ScheduleLessons', 'schedule_id', 'id');
    }

    public function student_tests()
    {
        return $this->hasMany('App\StudentTests', 'schedule_id', 'id');
    }

    public function student_paper_tests()
    {
        return $this->hasMany('App\StudentPaperTests', 'schedule_id', 'id');
    }

    public function assessment_users()
    {
        return $this->hasMany('App\AssessmentUsers', 'schedule_id', 'id');
    }

    public function course_schedule()
    {
        return $this->hasOne('App\CourseSchedules', 'schedule_id', 'id');
    }

    public function paper_tests()
    {
        return $this->hasMany('App\PaperTests', 'lesson_id', 'id');
    }

    public function off_days()
    {
        return $this->hasMany('App\ClassesOffDays', 'schedule_id', 'id');
    }

    public function comments() {
        return $this->hasMany('App\ScheduleComment', 'schedule_id', 'id');
    }

    public function files() {
        return $this->hasMany('App\ScheduleFile', 'schedule_id', 'id');
    }

    public function registered_students()
    {
        return $this->yoyaku()->where('waitlist', 0)->get();
    }

    public function waitlist_students()
    {
        return $this->yoyaku()->where('waitlist', 1)->get();
    }

    public function get_list_dates($only_future = 0)
    {
        $dates = [];

        if($this->type == 0) {
            $period = CarbonPeriod::create($this->start_date, $this->end_date);

            $function_check = 'is'.$this->day_of_week;

            foreach ($period as $date) {
                if($date->{$function_check}()) $dates[] = $date->format('Y-m-d');
            }
        } else {
            $dates[] = $this->date;
        }

        $today = Carbon::now(CommonHelper::getSchoolTimezone())->startOfDay();

        if($only_future == 1) // Future
        {
            $dates = collect($dates)->filter(function($value, $key) use ($today){
                return Carbon::createFromFormat('Y-m-d', $value, CommonHelper::getSchoolTimezone())->startOfDay() > $today;
            })->all();
        }
        else // Today & past
        {
            $dates = collect($dates)->filter(function($value, $key) use ($today){
                return Carbon::createFromFormat('Y-m-d', $value, CommonHelper::getSchoolTimezone())->startOfDay() <= $today;
            })->all();

             // Sort desc
             usort($dates, function($time1, $time2){
                if (strtotime($time1) < strtotime($time2))
                    return 1;
                else if (strtotime($time1) > strtotime($time2))
                    return -1;
                else
                    return 0;
            });
        }

        return $dates;
    }

    public function is_event() {
  	    return in_array($this->type, self::EVENT_TYPES) ? true : false;
    }

    public function is_class() {
        return in_array($this->type, self::CLASS_TYPES) ? true : false;
    }

    public function check_full($number = 0) {
        if($this->class->size) {
            return $this->yoyaku->count() + $number >= $this->class->size ? true : false;
        } else {
            return $this->yoyaku->count() + $number >= Settings::get_value('limit_number_of_students_per_class') ? true : false;
        }
    }

    public static function get_validate_params($type)
    {
        $validation_params = [
            'class_id' => 'required',
            'teacher_id' => 'required',
        ];

        switch($type) {
            case 'repeat':
                $validation_params['start_time'] = 'required';
                $validation_params['end_time'] = 'required';
                $validation_params['start_date'] = 'required|date';
                $validation_params['end_date'] = 'required|date|after_or_equal:start_date';
                break;
            case 'once':
                $validation_params['start_time'] = 'required';
                $validation_params['end_time'] = 'required';
                $validation_params['date'] = 'required';
                break;
            default:
                $validation_params['date'] = 'required';
        }

        return $validation_params;
    }

    public function get_type_label()
    {
        switch($this->type) {
            case 0:
                return __('messages.repeatclasslabel');
            case 1:
                return __('messages.onceoffclasslabel');
            case 2:
                return __('messages.event');
            case 3:
                return __('messages.alldayevent');
        }
    }

    public function is_repeat_class()
    {
        return $this->type == 0 ? true : false;
    }

    public function get_student_ids()
    {
        return $this->yoyaku()->where('waitlist', 0)->distinct('customer_id')->pluck('customer_id')->toArray();
    }

    public function get_students()
    {
        return Students::whereIn('id', $this->get_student_ids())->get();
    }

    public function get_tests()
    {
        $course = $this->course_schedule->course;

        if($course) {
            $course_id = $course->id;

            return Tests::whereIn('course_id', function($query) use ($course_id) {
                $query->select('course_id')
                      ->from('schedules')
                      ->where('id', $this->id);
            })->get();
        } else {
            return collect();
        }
    }

    public function get_date()
    {
        switch($this->type) {
            case 0:
                return $this->start_date.' -- '.$this->end_date;
                break;
            default:
                return $this->date;
        }
    }

    public function get_paper_tests()
    {
        $paper_tests = collect();

        if($this->course_schedule) {
            $course = $this->course_schedule->course;

            if($course->paper_tests->count() > 0) {
                foreach($course->paper_tests as $paper_test) {
                    $paper_tests->push($paper_test);
                }
            }
        }

        return $paper_tests;
    }

    public function valid_date($date)
    {
        foreach($this->off_days as $off_day) {
            if($off_day->date === $date) return false;
        }

        return true;
    }

    public static function get_calendar_views()
    {
        return [
            'month' => __('messages.month'),
            'agendaWeek' => __('messages.week'),
            'agendaDay' => __('messages.day'),
        ];
    }

    public function is_reserved_user($user)
    {
        $student = $user->student;

        if($student) {
            $yoyakus = $this->yoyaku;

            if($yoyakus->count() > 0) {
                foreach($yoyakus as $yoyaku) {
                    if($yoyaku->customer_id === $student->id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function isStudentRegistered($user,$check_date = '')
    {
        if($check_date == '')
        {
            return false;
        }
        $student = $user->student;
        if(!$student)
        {
            return false;
        }
        return $this->yoyaku()->where('customer_id',$student->id)
                            ->where('status','<>',2)
                            ->where('waitlist',0)
                            ->where(function($query) use ($check_date){
                                $query->where('date', $check_date);
                                $query->orWhere(function($query) use ($check_date){
                                    $query->where('start_date', '<= ', $check_date);
                                    $query->where('end_date', '>= ', $check_date);
                                });
                            })
                            ->count() > 0 ? true : false;
    }

    public function isEmpty($check_date)
    {
        return $this->yoyaku()->where('status','<>',2)
                    ->where('waitlist',0)
                    ->where('date', $check_date)
                    ->exists() ? false : true;
    }

    public function isWaitlisted($check_date)
    {
        return $this->yoyaku()->where('status','<>',2)
                    ->where('waitlist',1)
                    ->where('date', $check_date)
                    ->exists() ? true : false;
    }

    public function getFirstReservation($check_date)
    {
        return $this->yoyaku()
                    ->where('status','<>',2)
                    ->where('waitlist',0)
                    ->where('date', $check_date)
                    ->first();
    }

    public function isTimePassed($date = NULL)
    {
        $now = Carbon::now(CommonHelper::getSchoolTimezone());

        $event_date = $this->date;
        if ($this->type == self::CLASS_REPEATED_TYPE)
        {
            $event_date = $date;
        }

        $event_start = Carbon::parse($event_date . ' ' . $this->start_time, CommonHelper::getSchoolTimezone());
        if($this->type == self::EVENT_ALLDAY_TYPE)
        {
            $event_start->startOfDay();
        }
        return $event_start->lessThanOrEqualTo($now) ? true : false;
    }

    public function isPastClassCheckPasses($user, $date)
    {
        if($user->hasPermissionTo('allow-reservation-on-past-classes'))
        {
            return true;
        }
        return !$this->isTimePassed($date);
    }

    public function getScheduledLesson($lesson_id)
    {
        return $this->schedule_lessons()->where('lesson_id',$lesson_id)->first();
    }

    public function getScheduleZoomMeeting($date)
    {
        return ScheduleZoomMeeting::where('schedule_id', $this->id)->where('date',$date)->first();
    }

    public function the_files_url()
    {
        $html = '<div class="files-list">';
        foreach($this->files as $file)
        {
            if ($file) {

            }
            $html.= "<a href='".htmlspecialchars(Storage::disk('public')->url($file->file),ENT_QUOTES)."' target='_blank'>".(empty($file->file_name) ? basename($file->file) : $file->file_name)."</a><button data-type='schedule' data-id='".$file->id."' data-name='".(empty($file->file_name) ? basename($file->file) : $file->file_name)."' class='btn btn-defualt btn_file_name_edit'><i class='fa fa-pencil'></i></button><br>";
        }
        return $html."</div>";
    }

    
}
